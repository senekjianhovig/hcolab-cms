<?php

namespace hcolab\cms\traits;

trait ApiTrait
{

    public function responseError($code , $title = "" ,$message = "" , $status_code = 400){
        return response([
            'code' => $code,
            'type' => 'NOTIFICATION',
            'error' => [
                'title' => $title,
                'message' => $message
            ]
            ] , $status_code);
    }

    public function responseValidationError($code, $data ,$status_code = 400){

       if(request()->has('validations_response') && request()->input('validations_response') == 'array'){
            $errors = [];

            foreach($data->toArray() as $key => $value){
                $errors [] = [
                    'field' => $key,
                    'validations' => $value 
                ];
            }

            return response([
                'code' => $code,
                'type' => 'VALIDATION',
                'error' => $errors
            ] , $status_code);
        }

        return response([
            'code' => $code,
            'type' => 'VALIDATION',
            'error' => $data
        ] , $status_code);
    }

    public function responseSuccess($code , $title = "" ,$message = "" , $status_code = 200){

        return response([
            'code' => $code,
            'type' => 'NOTIFICATION',
            'success' => [
                'title' => $title,
                'message' => $message
            ]
        ] , $status_code);
    }

    public function responseData($code , $data , $status_code = 200){

        return response([
            'code' => $code,
            'data' => $data,
        ] , $status_code);

    }

}