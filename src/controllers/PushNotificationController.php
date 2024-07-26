<?php



namespace hcolab\cms\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use hcolab\cms\traits\ApiTrait;
use hcolab\cms\models\CmsPushNotification;
use hcolab\cms\models\CmsSentPushNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Kutia\Larafirebase\Facades\Larafirebase;
use Carbon\Carbon;

class PushNotificationController extends Controller
{

    use ApiTrait;


    // Create notification from Grid
    public function save(){

        $label = request()->input("label");
        $path = request()->input("path");

        $url = str_replace(["/cms/" ] , ["/api/v1/"] , $path);

        $notification = new CmsPushNotification;
        $notification->label = $label;
        $notification->link = $path;
        $notification->api = $url;
        $notification->page_slug = request()->input("page_slug");
        $notification->save();

        return redirect("/cms/page/cms-push-notifications");
    }


    // Send notification

    public function send($id , $token){

        $notification = CmsPushNotification::find($id);
        $page = (new \hcolab\cms\controllers\PageController)->initializeRequest($notification->page_slug);

        if (is_null($page)) { return abort(404); }

        $key = $page->push_notification_key;
        $entity_page = $page->push_notification_page;

        $response = Http::get($notification->api);

        if(!$response->successful()){ return abort(404); }

        $result = collect($response->json())->pluck($key)->unique()->values()->toArray();

        $target = new $entity_page;

        $rows = DB::table($target->entity)->select('id', 'device_token')->whereIn('id' , $result)->get();

        if(count($rows) == 0){
            return redirect("/cms/page/cms-push-notifications?notification_type=error&notification_message=Failed!");
        }


        $this->sendByProvider($notification , $rows , $target->model);


        return redirect("/cms/page/cms-push-notifications?notification_type=success&notification_message=Success!");
    }


    public function sendByProvider($notification , $rows , $model , $dictionary = []){

        if(is_numeric($notification)){
            $notification = CmsPushNotification::find($notification);
        }

        $notification->title = replace_from_dictionary($notification->title , $dictionary);
        $notification->message = replace_from_dictionary($notification->message , $dictionary);
        $notification->text = replace_from_dictionary($notification->text , $dictionary);


        if(!$notification){
            return null;
        }

        $players = $rows->pluck('device_token');

        switch(env('PUSH_NOTIFICATION')){
            case 'onesignal' :  $this->sendByOneSignal($notification->id , $notification->title , $notification->message , get_media_url($notification->image , 'jpg' , 'optimized') , $players , $notification->btn_link); break;
            default: $this->sendByFirebase($notification->id , $notification->title , $notification->message , get_media_url($notification->image , 'jpg' , 'optimized') , $players , $notification->btn_link); break;
        }

        $data = $rows->map(function($row) use ($notification , $model , $dictionary){
            return [
                'row_id' => $row->id,
                'row_model' => $model,
                'device_token' => $row->device_token,
                'notification_id' => $notification->id,
                'dictionary' => is_array($dictionary) ? json_encode($dictionary) : $dictionary,
                'read' => 0
            ];
        })->toArray();

        CmsSentPushNotification::insert($data);

        return true;
    }


    public function sendByOneSignal($id , $title ,  $text , $image_url , $player_ids , $btn_link = null){

        $data = ["type" => 1 , "notification_id" => $id];

        if($btn_link){
            $data['link_to'] = $btn_link;
        }

        $fields = [
            'app_id' => env('ONE_SIGNAL_APP_ID'),
            'data' => $data,
            'contents' => [ "en" => $text ],
            'headings' => [ "en" => $title ],
            'content_available' => true,
            'mutable_content' => true,
            'background_data' => true,
            'android_background_data' => true,
            'priority' => 10,
            'category' => "APP",
            "include_player_ids" => $player_ids
        ];

        if($image_url){
            $fields["ios_attachments"] =  array("id"=> $image_url);
            $fields["huawei_big_picture"] =  $image_url;
            $fields["big_picture"] =  $image_url;
            $fields["large_icon"] =  $image_url;
        }

        $fields = json_encode($fields);


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic '.env('ONE_SIGNAL_AUTH_KEY')));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = (curl_exec($ch));
        curl_close($ch);

        $response = json_decode($response,1);

        return $response;

    }

    public function sendByFirebase($id , $title , $text , $image_url , $player_ids, $btn_link = null){
       $notification =  Larafirebase::withTitle($title)
        ->withBody($text)
        ->withSound('default');

        if($image_url){
            $notification->withImage($image_url);
        }


        try {
            $player_ids = $player_ids->toArray();
        } catch (\Throwable $th) {
            $player_ids = [];
        }
        

        return $notification->withPriority('high')->sendNotification($player_ids);
    }


    // Get all notifications

    public function getNotifications(){

        $row_id = request()->row_id;
        $row_model = request()->row_model;

        $notifications = CmsPushNotification::select([
            'cms_sent_push_notifications.id',
            'cms_push_notifications.title',
            'cms_push_notifications.message',
            'cms_push_notifications.text',
            'cms_push_notifications.image',
            'cms_sent_push_notifications.created_at',
            'cms_push_notifications.btn_label',
            'cms_push_notifications.btn_link',
            'cms_sent_push_notifications.read',
            'cms_sent_push_notifications.dictionary',
            ])
            ->where('cms_sent_push_notifications.row_id' , request()->row_id)
            ->where('cms_sent_push_notifications.row_model' , request()->row_model)
            ->join('cms_sent_push_notifications' ,'cms_push_notifications.id' , 'cms_sent_push_notifications.notification_id')
            ->where('cms_sent_push_notifications.deleted' , 0)
            ->orderBy('id' , 'DESC')
            ->paginate(20)->map(function($notification){
                    return [
                        'id' => $notification->id,
                        'title' => replace_from_dictionary($notification->title , $notification->dictionary),
                        'message' => replace_from_dictionary($notification->message , $notification->dictionary),
                        'text' => replace_from_dictionary($notification->text , $notification->dictionary),
                        'created_at'=> $notification->created_at,
                        'image' => get_media_url($notification->image , 'jpg' , 'optimized'),
                        'btn_label' => $notification->btn_label,
                        'btn_link' => $notification->btn_link,
                        'read'=> $notification->read
                    ];
            });

        return $this->responseData(1, $notifications);

    }


    // Set all notifications as read
    public function setAllNotificationsRead(){

        $notification_ids = CmsSentPushNotification::where('row_id' , request()->row_id)
        ->where('row_model' , request()->row_model)
        ->where('deleted' , 0)
        ->update(['read' => 1]);

        return response()->json(["nb_unread_notifications" => 0] , 200);

    }


    // Set a notification as read
    public function setNotificationsRead(){

        $notification_id = request()->notification_id;

        $notification_ids = CmsSentPushNotification::where('row_id' , request()->row_id)
        ->where('row_model' , request()->row_model)
        ->where('id' , $notification_id)
        ->where('deleted' , 0)
        ->update(['read' => 1]);
        
        $unread_notifications =  CmsSentPushNotification::where('row_id' , request()->row_id)
        ->where('row_model' , request()->row_model)
        ->where('deleted' , 0)->where('read', 0)
        ->count();
        

        return response()->json([
               'nb_unread_notifications' => $unread_notifications
            ] , 200);

    }


}
