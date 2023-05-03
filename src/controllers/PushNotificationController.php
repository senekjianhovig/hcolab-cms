<?php



namespace hcolab\cms\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use hcolab\cms\traits\ApiTrait;
use hcolab\cms\models\CmsPushNotification;
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

        // $response = Http::get($notification->api);

        // if(!$response->successfull()){
        //     return abort(404);
        // }

        // $result = collect($response->json())->pluck($key)->unique()->values()->toArray();

        $result = [1,2,3];

        $target = new $entity_page;
        
        $firebaseTokens = DB::table($target->entity)->select('device_token')->whereIn('id' , $result)->pluck('device_token')->unique()->filter()->values()->toArray();

        if(count($firebaseTokens) == 0){

        }
        
        Larafirebase::withTitle($notification->title)
        ->withBody($notification->message)
        ->sendMessage($firebaseTokens);

        return redirect("/cms/page/cms-push-notifications");
    }


}