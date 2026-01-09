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
        
        // Process repeater fields - convert array data to JSON and handle file uploads
        $repeater_inputs = collect($section_model->elements)->where('ui.type', 'repeater')->values();
        foreach($repeater_inputs as $repeater_input){
            $repeater_name = $repeater_input->name;
            $repeater_fields = $repeater_input->ui->fields ?? [];
            $repeater_data = [];
            
            // Process file uploads for repeater fields first
            foreach($repeater_fields as $fieldName => $fieldType){
                if($fieldType == 'file' || $fieldType == 'image'){
                    // Find temporary files for this repeater field
                    // Check both bracket notation (tmp_cards[0][image]) and bracket-free (tmp_upld_cards_0_image)
                    foreach($payload as $item){
                        $is_repeater_file = false;
                        $index = null;
                        
                        // Check bracket notation: tmp_cards[0][image]
                        if(strpos($item['name'], 'tmp_'.$repeater_name.'[') === 0 && strpos($item['name'], '['.$fieldName.']') !== false){
                            preg_match('/\[(\d+)\]\[([^\]]+)\]/', $item['name'], $matches);
                            if(count($matches) == 3 && $matches[2] == $fieldName){
                                $index = $matches[1];
                                $is_repeater_file = true;
                            }
                        }
                        // Check bracket-free notation: tmp_upld_cards_0_image
                        elseif(strpos($item['name'], 'tmp_upld_'.$repeater_name.'_') === 0 && strpos($item['name'], '_'.$fieldName) !== false){
                            // Extract index from bracket-free name: tmp_upld_cards_0_image -> index: 0
                            $pattern = '/tmp_upld_'.$repeater_name.'_(\d+)_'.$fieldName.'/';
                            if(preg_match($pattern, $item['name'], $matches)){
                                $index = $matches[1];
                                $is_repeater_file = true;
                            }
                        }
                        
                        if($is_repeater_file && $index !== null){
                            $temporary = \hcolab\cms\models\TemporaryFile::where('name', $item['value'])
                                ->where(function($q){
                                    $q->whereNull('deleted_at');
                                    $q->orWhere('deleted', 0);
                                })
                                ->first();
                            if($temporary){
                                $uploaded_file = (new FileUploadController)->createFileFromTemporary($temporary, null);
                                // Update payload with actual file name using bracket notation
                                $actual_field_name = $repeater_name.'['.$index.']['.$fieldName.']';
                                $payload = $payload->map(function($current_payload) use ($repeater_name, $index, $fieldName, $item, $uploaded_file, $actual_field_name){
                                    if($current_payload['name'] == $item['name'] || 
                                       $current_payload['name'] == $actual_field_name ||
                                       $current_payload['name'] == $repeater_name.'['.$index.']['.$fieldName.']'){
                                        return ["name" => $actual_field_name, "value" => $uploaded_file->name];
                                    }
                                    return $current_payload;
                                });
                            }
                        }
                    }
                }
            }
            
            // Collect all fields for this repeater
            foreach($payload as $item){
                if(strpos($item['name'], $repeater_name.'[') === 0 && strpos($item['name'], 'tmp_') === false){
                    // Extract index and field name: cards[0][title] -> index: 0, field: title
                    preg_match('/\[(\d+)\]\[([^\]]+)\]/', $item['name'], $matches);
                    if(count($matches) == 3){
                        $index = $matches[1];
                        $field = $matches[2];
                        if(!isset($repeater_data[$index])){
                            $repeater_data[$index] = [];
                        }
                        $repeater_data[$index][$field] = $item['value'];
                    }
                }
            }
            
            // Convert to indexed array (don't JSON encode - the whole payload will be encoded later)
            $repeater_data = array_values($repeater_data);
            
            // Remove old repeater items from payload (including tmp_ ones)
            $payload = $payload->filter(function($item) use ($repeater_name){
                return strpos($item['name'], $repeater_name.'[') !== 0 && strpos($item['name'], 'tmp_'.$repeater_name.'[') !== 0;
            });
            
            // Add array value (will be JSON encoded when whole payload is saved)
            $payload->push(['name' => $repeater_name, 'value' => $repeater_data]);
        }
        
        $file_inputs = collect($section_model->elements)->where('ui.type', 'file')->values();
   
        $to_remove = [];
   
        foreach($file_inputs as $file_input){
           $file = collect($payload)->where('name' , 'tmp_'.$file_input->name)->first();
           if(!$file){
               continue;
           }
           $temporary = \hcolab\cms\models\TemporaryFile::where('name' , $file["value"] ) 
           ->where(function($q){
            $q->whereNull('deleted_at');
            $q->orWhere('deleted' , 0);
          })
           ->first();
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
    
      // Always save as values array (numeric indices starting from 0)
      $payload = collect($payload)->values();
        
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

        // Always save as values array (numeric indices starting from 0)
        $payload = collect($payload)->values();

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
        $section->deleted_at = now();
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
            // Convert payload to array if it's an object with numeric keys
            if(is_object($payload)){
                $payload = collect($payload)->values()->toArray();
            }
            if(!is_array($payload)) { $payload = []; }

            
       
            foreach($payload as $payload_item){
               

                if(str_contains($payload_item->name, '[]')){
                   
                    // dd($data);
                    $key = str_replace('[]' , '' , $payload_item->name);
                    if(!isset($data[$key])){ $data[$key] = []; }
                    $data[$key] [] = $payload_item->value;     
                }else{
                    // Check if this is a repeater field - if value is already an array, use it directly
                    // Otherwise, it might be a JSON string that needs decoding (for backward compatibility)
                    $value = $payload_item->value;

                   
                    if(is_string($value) && (substr($value, 0, 1) === '[' || substr($value, 0, 1) === '{')){
                        // Try to decode if it looks like JSON (for backward compatibility with old data)
                        
                      

                        $decoded = json_decode($value, true);
                        if(json_last_error() === JSON_ERROR_NONE){
                            $value = $decoded;
                        }
                    }
                    $data[$payload_item->name] = $value;
                   
                }
            } 
       
            $data = json_decode(json_encode($data));
        }else{
            $data = new \StdClass;
        }

        // dd($data);

       

    
        
         return view('CMSViews::components.theme-builder-section' , ['data' => $data , 'section' => $section_model]);
    }


    public function sectionOrdering($id){

        $array = request()->input('sorting' , []);

        
        $max_orders = CmsThemeBuilderSection::max('orders');

        if(!$max_orders){
            $max_orders = 0;
        }

        

    
        $sections = CmsThemeBuilderSection::query()
        ->where(function($q){
            $q->whereNull('deleted_at');
            $q->orWhere('deleted' , 0);
          })
        
        ->orderBy('orders' , 'ASC')->where('theme_builder_id' , $id)->get();

        $numbers = $sections->pluck('orders')->filter()->values()->toArray();


        while (count($sections) != count($numbers)) {
            $numbers [] = $max_orders + 1;
            $max_orders++; 
        }


        $sections =  $sections->keyBy('id');
       
        

       

        foreach($array as $i => $item){

            
            if(!isset($sections[$item]) || !isset($numbers[$i])){
                continue;
            }

            $record = $sections[$item];
            $record->orders = $numbers[$i];
            $record->save();

        }
       
        


        return response()->json([] , 200);
    }


    public function renderSections($location){

        $theme_builder = CmsThemeBuilder::query()
         ->where(function($q){
            $q->whereNull('deleted_at');
            $q->orWhere('deleted' , 0);
          })
        ->where('cms_theme_builder_location' , $location)->where('publish', 1)->orderBy('id' , 'DESC')->first();
    
        if(!$theme_builder){
            return abort(403 , "No theme found from the CMS");
        }
   
       $components = CmsThemeBuilderSection::query()
        ->where(function($q){
            $q->whereNull('deleted_at');
            $q->orWhere('deleted' , 0);
          })
       ->orderBy('orders' , 'ASC')->where('theme_builder_id' , $theme_builder->id)->get();
    
    
       $config = config('pages');
   
       
       $result = [];
       
       
       foreach($components as $component){
       
       
           $arr = [];
          
           $namespace = 'App\\Sections\\' . $component->name;
           $section =  new $namespace;
   
           $foreign_keys = collect($config['foreign_keys'])->whereIn('name' , $section->foreign_keys)->pluck('format' , 'name')->map(function($key){
               
               $arr = explode(",",$key);
               $length = count($arr);
               $key = $arr[$length - 1];
               return explode(":" , $key)[0];
           });
           
    
        //     if($component->id == 10){
        //       dd($foreign_keys);
        //   }
   
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
                         
                               
                         $data = DB::table($foreign_keys[$element->name])
                        
                         ->where(function($q){
                            $q->whereNull('deleted_at');
                            $q->orWhere('deleted' , 0);
                          })

                         ->whereIn('id' ,$arr[$element->name]);
                       
                    //   $str = 'FIELD(id,'.implode(",",$arr[$element->name]).')';

                    //      $data->orderByRaw($str); 
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
                           $data = DB::table($foreign_keys[$element->name])
                           ->where(function($q){
                            $q->whereNull('deleted_at');
                            $q->orWhere('deleted' , 0);
                          })
                           ->where('id' ,$arr[$element->name])->first();
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