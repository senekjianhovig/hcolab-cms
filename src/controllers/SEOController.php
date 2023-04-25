<?php

namespace hcolab\cms\controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\Sitemap\SitemapGenerator;
use Illuminate\Support\Facades\Storage;
use hcolab\cms\models\CmsSEO;

class SEOController extends Controller
{

    public function render(){
        
        $result = Storage::disk('public')->get('sitemap.xml');

       if(!$result){ 
           return false; 
        }

       $cms_seo = CmsSEO::where('deleted' , 0)->get()->keyBy('url');

       $array = json_decode(json_encode(simplexml_load_string($result)),1);
       $res = $array['url'];
       $urls = [];


       foreach($res as $url){
        $href = str_replace(['https://' , 'http://'] , '' , $url['loc']);
        $uri = str_replace(substr($href, 0, strpos($href, "/")) , '' , $href);

        if(str_contains($uri , 'storage')){ continue; }
        $urls [] = [
            'url' => $uri,
            // 'exist' => isset($cms_seo[$uri]) ? true : false,
            // 'empty' => !isset($cms_seo[$uri]) || (isset($cms_seo[$uri]) && (!$cms_seo[$uri]->title || !$cms_seo[$uri]->description)),
            'title' => isset($cms_seo[$uri]->title) ? $cms_seo[$uri]->title : "",
            'description' => isset($cms_seo[$uri]->description) ? $cms_seo[$uri]->description : "",
            'keywords' => isset($cms_seo[$uri]->keywords) ? $cms_seo[$uri]->keywords : ""
        ]; 
       }



       return view('CMSViews::page.seo-configuration' , compact('urls'));
  
    }


    public function renderSEO(){
        $path = request()->path();
        if($path[0] != "/"){
            $path = '/'.$path;
        }

        $seo =  CmsSEO::where('url' , $path)->whereNotNull('title')->where('deleted' , 0)->first();
        $default_seo =  CmsSEO::where('url' , "/")->whereNotNull('title')->where('deleted' , 0)->first();

        return view('CMSViews::page.seo' , compact('seo' , 'default_seo'));
    }

    public function renderModify(){

        $urls = request()->input('url');

        if(!is_array($urls)){
            return redirect()->route('seo-configuration');     
        }

        $data = new \StdClass;

        if(count($urls) == 1){

            $data =  CmsSEO::whereIn('url' , $urls)->where('deleted' , 0)->first();
       
            if(!$data){
                $data = new \StdClass;
            }else{
                $data = json_decode(json_encode($data));
            }

        }
        

        return view('CMSViews::page.seo-modify' , compact('urls' , 'data'));

    }


    public function modify(){


        try {
            $urls = json_decode(request()->input('urls') , 1);
        } catch (\Throwable $th) {
            $urls = [];
        }
      
       
        if(!is_array($urls)){
            return redirect()->route('seo-configuration');     
        }
       

        foreach($urls as $url){

            $seo = CmsSEO::where('url' , $url)->where('deleted' , 0)->first();
            if(!$seo){ $seo = new CmsSEO; }

            $seo->url = $url;
            $seo->title = request()->input('title');
            $seo->description = request()->input('description');
            $seo->keywords = request()->input('keywords');
            $seo->save();
        }


        return redirect()->route('seo-configuration');

    }

    
   
}