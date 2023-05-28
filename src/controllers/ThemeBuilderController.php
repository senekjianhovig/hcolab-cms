<?php

namespace hcolab\cms\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use hcolab\cms\traits\ApiTrait;
use hcolab\cms\models\CmsThemeBuilderSection;
use hcolab\cms\models\CmsThemeBuilder;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

class ThemeBuilderController extends Controller
{

    use ApiTrait;


    public function processFile($section_name , $payload){


        $namespace = 'App\\Sections\\' . $section_name;
        $section_model =  new $namespace;
   
        $payload = collect($payload);

        $section_model->setElements();
        $file_inputs = collect($section_model->elements)->where('ui.type', 'file')->values();
   
        $to_remove = [];
   
        foreach($file_inputs as $file_input){
           $file = collect($payload)->where('name' , 'tmp_'.$file_input->name)->first();
           if(!$file){
               continue;
           }
           $temporary = \hcolab\cms\models\TemporaryFile::where('name' , $file["value"] )->where('deleted',0)->first();
           $uploaded_file = (new FileUploadController)->createFileFromTemporary($temporary, $file_input->ui->resize);            
           

           $payload = $payload->map(function($current_payload) use($file_input , $uploaded_file){
                if($current_payload['name'] == "tmp_".$file_input->name || $current_payload['name'] == $file_input->name){
                    return ["name" => $file_input->name , "value" => $uploaded_file->name];
                }else{
                    return ["name" => $current_payload["name"] , "value" => $current_payload["value"]];
                }
           })->unique()->values();
       }
   
    return $payload;

    }

    public function store(){


      $payload = $this->processFile(request()->input('name'), request()->input('payload'));
    
        
      $section = new CmsThemeBuilderSection;
      $section->payload = json_encode($payload);
      $section->title = request()->input('title');
      $section->name = request()->input('name');
      $section->theme_builder_id = request()->input('theme_builder_id');
      $section->save();



      return $section->id;

    }

    public function update()
    {

      
        $section = CmsThemeBuilderSection::find(request()->input('id'));

        if(!$section){ return response()->json([] , 400); }

        $payload = $this->processFile($section->name, request()->input('payload'));

        $section->payload = json_encode($payload);
        $section->save();
  
        return $section->id;
    }


    public function deleteSection($section_model, $key){

        $section = CmsThemeBuilderSection::find($key);
        
        if(!$section){
            return response()->json([], 404);
        }

        $section->deleted = 1;
        $section->save();

        return response()->json([], 200);

    }

    public function section($section_model){
        $data = [];

        if(request()->input('edit_mode') && request()->input('key')){

            $section = CmsThemeBuilderSection::find(request()->input('key'));

            try{
                $payload = json_decode($section->payload);
            }catch(\Throwable $th){
                $payload = [];
            }

          //  [{"name":"section_title","value":"Slideshow"},{"name":"section_name","value":"SlideshowSection"},{"name":"slideshow_id[]","value":"1"}]


            //dd($payload);

            // dd($payload);
            if(!is_array($payload)) { $payload = []; }

            
            foreach($payload as $payload_item){
               

                if(str_contains($payload_item->name, '[]')){
                   
                    // dd($data);
                    $key = str_replace('[]' , '' , $payload_item->name);
                    if(!isset($data[$key])){ $data[$key] = []; }
                    $data[$key] [] = $payload_item->value;     
                }else{
                    
                    $data[$payload_item->name] = $payload_item->value;
                   
                }
            } 
       
            $data = json_decode(json_encode($data));
        }else{
            $data = new \StdClass;
        }

        // dd($data);

       

    
        
         return view('CMSViews::components.theme-builder-section' , ['data' => $data , 'section' => $section_model]);
    }


    public function renderSections($location){

        $theme_builder = CmsThemeBuilder::where('deleted',0)->where('cms_theme_builder_location' , $location)->where('publish', 1)->orderBy('id' , 'DESC')->first();
    
        if(!$theme_builder){
            return abort(403 , "No theme found from the CMS");
        }
   
       $components = CmsThemeBuilderSection::where('deleted',0)->orderBy('orders' , 'ASC')->where('theme_builder_id' , $theme_builder->id)->get();
    
    
       $config = config('pages');
   
       
       $result = [];
       
       foreach($components as $component){
       
       
           $arr = [];
          
           $namespace = 'App\\Sections\\' . $component->name;
           $section =  new $namespace;
   
           $foreign_keys = collect($config['foreign_keys'])->whereIn('name' , $section->foreign_keys)->pluck('format' , 'name')->map(function($key){
               return explode(":" , $key)[0];
           });
           
   
   
   
           try{
               $payload = json_decode($component->payload);
           }catch(\Throwable $th){
               $payload = [];
           }
   
           if(!is_array($payload)) { $payload = []; }
   
           
           foreach($payload as $payload_item){
              
               if(str_contains($payload_item->name, '[]')){
                   $key = str_replace('[]' , '' , $payload_item->name);
                   if(!isset($arr[$key])){ $arr[$key] = []; }
                   $arr[$key] [] = $payload_item->value;     
               }else{
                   $arr[$payload_item->name] = $payload_item->value;
               }
           } 
   
           $section->setElements();
   
           $elements = $section->elements;
   
           
           $view = [];
   
           foreach($elements as $element){
               switch($element->ui->type){
                   case 'multiple select' :
                     if(isset($arr[$element->name])){
                         
                        
                         $data = DB::table($foreign_keys[$element->name])->where('deleted',0)->whereIn('id' ,$arr[$element->name]);
                        
                         if (Schema::hasColumn($foreign_keys[$element->name], 'orders')){
                            $data->orderBy('orders' , 'ASC');
                         }

                         $data = $data->get();
   
                     }else{
                         $data = [];
                     }
   
                   break;
                   
                   case 'select':
                       if(isset($arr[$element->name])){
                           $data = DB::table($foreign_keys[$element->name])->where('deleted',0)->where('id' ,$arr[$element->name])->first();
                       }else{
                           $data = null;
                       }
   
                   default:
                       if(isset($arr[$element->name])){
                           $data = $arr[$element->name];
                       }else{
                           $data = null;
                       }
                   break;
               }
   
               $view[$element->name] = $data;
           }
   
          
           $result [] = [
                   'type' => $section->identifier,
                   'payload' => $view
           ];
       }

       return $result;
    }
}