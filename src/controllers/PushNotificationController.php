<?php



namespace hcolab\cms\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use hcolab\cms\traits\ApiTrait;


class PushNotificationController extends Controller
{

    use ApiTrait;


    public function sendNotification()
    {
        //$firebaseToken = User::whereNotNull('device_token')->pluck('device_token')->all();

        
    }
}