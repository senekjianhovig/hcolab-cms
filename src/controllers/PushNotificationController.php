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

class PushNotificationController extends Controller
{

    use ApiTrait;


    public function sendNotification($title , )
    {

      

        //$firebaseToken = User::whereNotNull('device_token')->pluck('device_token')->all();

    }

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

        if(!$response->successfull()){
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
                'device_token' => $ft,
                'notification_id' => $id,
                'read' => 0
            ];
        });

        CmsSentPushNotification::insert($data);

        Larafirebase::withTitle($notification->title)
        ->withBody($notification->message)
        ->sendMessage($firebaseTokens);

        return redirect("/cms/page/cms-push-notifications?notification_type=success&notification_message=Success!");
    }

    public function getNotifications(){

        $device_token = request()->device_token;
        $notification_ids = CmsSentPushNotification::select('notification_id')->where('device_token' , $device_token)->where('deleted' , 0)->pluck('notification_id');
        $notifications = CmsPushNotification::whereIn('id' , $notification_ids )->where('deleted', 0)->get()->paginate(20)->map(function($notification){
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'text' => $notification->text,
                'created_at'=> $notification->created_at,
                'image' => get_media_url($notification->image)
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
        ->where('notification_id' , $notification_id)
        ->where('deleted' , 0)
        ->update(['read' => 1]);
       
        return response()->json([] , 200);

    }


}