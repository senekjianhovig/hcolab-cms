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

    public function send($id , $token){

        $notification = CmsPushNotification::find($id);
        $page = (new \hcolab\cms\controllers\PageController)->initializeRequest($notification->page_slug);
       
        if (is_null($page)) {
            return abort(404);
        }

        $key = $page->push_notification_key;
        $entity_page = $page->push_notification_page;

        $response = Http::get($notification->api);

        if(!$response->successful()){
            return abort(404);
        }

        $result = collect($response->json())->pluck($key)->unique()->values()->toArray();

        // $result = [1,2,3];

        $target = new $entity_page;
        
        $firebaseTokens = DB::table($target->entity)->select('device_token')->whereIn('id' , $result)->pluck('device_token')->unique()->filter()->values()->toArray();

        if(count($firebaseTokens) == 0){
            return redirect("/cms/page/cms-push-notifications?notification_type=error&notification_message=Failed!");
        }
        
        // $notification->device_tokens = json_encode($firebaseTokens);
        // $notification->save();

        $data = collect($firebaseTokens)->map(function($fT) use ($id){
            return [
                'device_token' => $fT,
                'notification_id' => $id,
                'read' => 0
            ];
        })->toArray();

        CmsSentPushNotification::insert($data);

        if(env('PUSH_NOTIFICATION') == 'onesignal'){
            $this->sendByOneSignal($notification->title , $notification->message , null , $firebaseTokens);
        }else{
            Larafirebase::withTitle($notification->title)
            ->withBody($notification->message)
            ->withPriority('high')
            ->sendNotification($firebaseTokens);
        }

       

        return redirect("/cms/page/cms-push-notifications?notification_type=success&notification_message=Success!");
    }



    public function sendByOneSignal($title ,  $text , $image_url , $player_ids){

        $fields = [
            'app_id' => env('ONE_SIGNAL_APP_ID'),
            'data' => [],
            'contents' => [ "en" => $text ],
            'headings' => [ "en" => $title ],
            'content_available' => true,
            'mutable_content' => true,
            'background_data' => true,
            'android_background_data' => true,
            'priority' => 10,
            'category' => "TEST",
            "include_player_ids" => $player_ids
        ];

        if(!is_null($image_url)){
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


    public function getNotifications(){

        $device_token = request()->device_token;
        // $notification_ids = CmsSentPushNotification::select('notification_id')->where('device_token' , $device_token)->where('deleted' , 0)->pluck('notification_id');
        // $notifications = CmsPushNotification::whereIn('id' , $notification_ids )->where('deleted', 0)->paginate(20)->map(function($notification){
        //     return [
        //         'id' => $notification->id,
        //         'title' => $notification->title,
        //         'message' => $notification->message,
        //         'text' => $notification->text,
        //         'created_at'=> $notification->created_at,
        //         'image' => get_media_url($notification->image),
        //         'btn_label' => 'Check marketing website',
        //         'btn_link' => '#',
        //         'read'=> 0
        //     ];
        // });

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
            ])
            ->where('cms_sent_push_notifications.device_token' , $device_token)
            ->join('cms_sent_push_notifications' ,'cms_push_notifications.id' , 'cms_sent_push_notifications.notification_id')
            ->where('cms_sent_push_notifications.deleted' , 0)
            ->paginate(20)->map(function($notification){
                    return [
                        'id' => $notification->id,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'text' => $notification->text,
                        'created_at'=> $notification->created_at,
                        'image' => get_media_url($notification->image),
                        'btn_label' => $notification->btn_label,
                        'btn_link' => $notification->btn_link,
                        'read'=> $notification->read
                    ];
            });

        return $this->responseData(1, $notifications);

    }

    public function setAllNotificationsRead(){

        $device_token = request()->device_token;
        $notification_ids = CmsSentPushNotification::where('device_token' , $device_token)->where('deleted' , 0)
        ->update(['read' => 1]);

        return response()->json([] , 200);

    }

    public function setNotificationsRead(){

        $device_token = request()->device_token;
        $notification_id = request()->notification_id;
       
        $notification_ids = CmsSentPushNotification::where('device_token' , $device_token)
        ->where('id' , $notification_id)
        ->where('deleted' , 0)
        ->update(['read' => 1]);
       
        return response()->json([] , 200);

    }


}