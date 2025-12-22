<?php

namespace hcolab\cms\middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use hcolab\cms\models\AccessToken;
use Carbon\Carbon;

class PushNotificationAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {


        if (!request()->header('access-token')) {
            return response()->json(["error"=> ["message" => "Missing access token in the header"] ], 400);
        }



        $access_token = AccessToken::query()

        ->where(function($q){
            $q->whereNull('deleted_at');
            $q->orWhere('deleted' , 0);
          })

        ->where('expires_at', '>' , Carbon::now())
        ->where('token',request()->header('access-token'))->first();

        if(!$access_token){
            return response()->json(["error"=> ["message" => "Incorrect access token"] , "invalid_token" => true ], 400);
        }

        $tokenable_id = $access_token->tokenable_id;
        $tokenable_type = $access_token->tokenable_type;

        request()->merge([
            'row_id' => $tokenable_id,
            'row_model' => $tokenable_type
        ]);

        return $next($request);
    }
}
