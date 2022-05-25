<?php
//composer dump-autoload

if(!function_exists('process_grid_field')){
    function process_grid_field($row, $column , $related_tables){
       
            switch ($column->type) {
                case 'textfield':
                return $row->{$column->name};
                
                case 'primary_field':
                return "<div class='ui  label grey'>".$row->{$column->name}."</div>";
                
                case 'select':
                try { return $related_tables[$column->name]["indexed_data"][$row->{$column->name}]->label; } catch (\Throwable $th) { return "hovig"; }
                
                case 'multiple select':  

                return "Values";

                default:
                return $row->{$column->name};  
            }
      
    }
}

if(!function_exists('process_form_field')){
    function process_form_field($element , $data , $related_tables = null){
       
            switch ($element->ui->type) {
                case "external textfield":
                case "textfield":
                case "email":
                case "number":
                return view('CMSViews::form.textfield', [ "element" => $element, "data" => $data ]);
               
                break;
                case "disabled_textfield":
                return view('CMSViews::form.disabled-textfield', [ "element" => $element, "data" => $data ]);
                break;
                case "select":
                case "multiple select":
                return view('CMSViews::form.select', [ "element" => $element, "data" => $data , 'related_tables' =>
                $related_tables]);
                break;

                case "boolean checkbox":
                return view('CMSViews::form.boolean-checkbox', [ "element" => $element, "data" => $data ]);
                break;

                case "textarea":
                return view('CMSViews::form.textarea', [ "element" => $element, "data" => $data ]);
                break;

                case "password":
                return view('CMSViews::form.password', [ "element" => $element, "data" => $data ]);
                break;
                case "date picker":
                return view('CMSViews::form.datepicker', [ "element" => $element, "data" => $data ]);
                break;
                case "date time picker":
                return view('CMSViews::form.datetimepicker', [ "element" => $element, "data" => $data ]);
                break;

                case "wysiwyg":
                return view('CMSViews::form.wysiwyg', [ "element" => $element, "data" => $data ]);
                break;

                case "url":
                return view('CMSViews::form.url', [ "element" => $element, "data" => $data ]);
                break;

                case "file":
                case "multiple file":
                case "image":
                return view('CMSViews::form.file', [ "element" => $element, "data" => $data]);
                break;

                case 'open div':
                return "<div class='".$element->ui->classes."'>";
                break;

                case 'close div':
                return "</div>";
                break;  

                case 'variants panel':
                return view('CMSViews::form.variants-panel', [ "element" => $element, "data" => $data , "related_tables"=>$related_tables]);
                break;

                case 'text':
                return $element->ui->text;
                break;

                case 'values select':
                return view('CMSViews::form.select', [ "element" => $element, "data" => $data ]);
                break;

                case 'ecom inventory':
                case 'ecom pricing': 
                
                $target_page = new $element->target_page;
                $target_page->setElements(); 

                $result = [];
                foreach($target_page->elements as $elem){
                    if(in_array($elem->name ,$target_page->on_create)){
                        $result [] = process_form_field($elem , $data , $related_tables); 
                    }
                }

                return implode("\n" , $result);
               
                break;
                

            }
      
    }
}

if(!function_exists('set_db')){
    function set_db($field_name, $field_type,  $field_length, $field_default, $is_multi_language){
        $db = new \StdClass;
        $db->field_name = $field_name;
        $db->field_type = $field_type;
        $db->field_length = $field_length ? $field_length : null;
        $db->field_default = $field_default;
        $db->is_multi_language = $is_multi_language;

        return $db;
    }
}


if(!function_exists('set_id_index')){
    function set_id_index($rows){
      $array = [];
        foreach($rows as $row){
          $array[$row->id] = $row;
        }
        return $array;
    }
}

if(!function_exists('get_page_settings')){
    function get_page_settings($page){
        $page_str = str_replace(['Page', 'page'] , ['' , ''] , $page);
        
        $arr = explode('_',strtolower(preg_replace('/([a-z])([A-Z])/', "\\1_\\2", $page_str)));

        

        $size_arr = sizeof($arr);
        $arr[$size_arr-1] = \Illuminate\Support\Str::plural($arr[$size_arr-1], 2);

        $lower_array = array_map('strtolower', $arr);
        $upper_array = array_map('ucfirst', $arr);
        

        $entity = implode('_' , $lower_array);
        $slug = implode('-' , $lower_array);
        $title = implode(' ' , $upper_array);
        
        
        return ['entity' => $entity , 'slug' => $slug , 'title' => $title];
        
    }
}


if(!function_exists('get_name_from_url')){
    function get_name_from_url($name){
        $temp= explode('.',$name);
        array_pop($temp);
        return implode("." , $temp);
    }
}

if(!function_exists('get_name_from_urls')){
    function get_name_from_urls($names){
        $result = [];

        foreach($names as $name){
            $temp= explode('.',$name);
            array_pop($temp);
            $result [] = implode("." , $temp);
        }

       return $result;
    }
}





if(!function_exists('get_media_url')){

 function get_media_url($name, $extension = "jpg" , $type = "optimized" , $resized = null){

    if(is_null($name)){ return ""; }
    
    $temp= explode('.',$name);
    $original_extension = end($temp);
    array_pop($temp);
    $original_name = implode("." , $temp);

    if($type == "resized"){
        switch($extension){
            case  "jpg" : return env('APP_URL')."/storage/files/resized/jpg/".$resized."/".$original_name.".jpg";
            case  "webp" : return env('APP_URL')."/storage/files/resized/webp/".$resized."/".$original_name.".webp"; 
            default: return env('APP_URL')."/storage/files/resized/".$resized."/".$original_name.".".$original_extension;
        }
    }elseif($type == "optimized"){
        switch($extension){
            case  "jpg" : return env('APP_URL')."/storage/files/optimized/jpg/".$original_name.".jpg";
            case  "webp" : return env('APP_URL')."/storage/files/optimized/webp/".$original_name.".webp"; 
            default: return env('APP_URL')."/storage/files/optimized/".$original_name.".".$original_extension;
        }
    }else{
        return env('APP_URL')."/storage/files/original/".$original_name.".".$original_extension;
    }
}
}

if(!function_exists('get_media_urls')){
    function get_media_urls($names , $extension = "jpg" , $type = "optimized" , $resized = null){
            $result = [];
            if(is_null($names)){ return $result; }
            foreach($names as $name){ $result [] = get_media_url($name , $extension , $type , $resized); }
            return $result;
    }
}