<?php



namespace hcolab\cms\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use hcolab\cms\traits\ApiTrait;


class SettingController extends Controller
{

    use ApiTrait;



    public function render(){

        $settings = \hcolab\cms\models\Setting::query()->where(function($q){
            $q->whereNull('deleted_at');
            $q->orWhere('deleted' , 0);
          })->get();

        return view('CMSViews::page.settings' , compact('settings'));
    }

    public function save()
    {
       
        $settings = \hcolab\cms\models\Setting::query()
        ->where(function($q){
            $q->whereNull('deleted_at');
            $q->orWhere('deleted' , 0);
          })
        ->get();

        foreach($settings as $setting){
            if(request()->has($setting->key)){
                $setting->value = request()->input($setting->key);
                $setting->save();
            }
        }
        
        return redirect()->back();
        
    }
}