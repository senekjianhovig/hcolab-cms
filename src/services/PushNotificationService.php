<?php

namespace hcolab\cms\services;

class PushNotificationService
{

   
    private $title;
    private $body;
    private $deviceTokens;


    public function __constructor($title , $body , $deviceTokens){
        $this->title = $title; 
        $this->body = $body;
        $this->deviceTokens = $deviceTokens;
    }


    public function send(){

        $SERVER_API_KEY = env('FIREBASE_SERVER_API_KEY' , '');

        if($SERVER_API_KEY){
            return false;
        }
        

        $data = [
            "registration_ids" => $this->deviceTokens,
            "notification" => [
                "title" => $this->title,
                "body" => $this->body,
                "content_available" => true,
                "priority" => "high",
            ]
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);

        return true;
    }
}