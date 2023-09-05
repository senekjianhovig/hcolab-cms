<?php
 use hcolab\cms\mail\EmailTemplateMail;
 use Illuminate\Support\Facades\Mail;

if(!function_exists('process_grid_field')){
    function process_grid_field($row, $column , $related_tables){

            switch ($column->type) {
                case 'textfield':
                return $row->{$column->name};

                case 'primary_field':
                return "<div class='ui  label grey'>".$row->{$column->name}."</div>";

                case 'select':
                    // dd($column);
                try { return $related_tables[$column->name]["indexed_data"][$row->{$column->name}]->label; } catch (\Throwable $th) { return ""; }

                case 'multiple select':

                return "Values";


                case "boolean checkbox":

                    return $row->{$column->name} == 0 ?  '<i class="red x icon"></i>' : '<i class="green checkmark icon"></i>';

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

                case "hidden_textfield":
                return view('CMSViews::form.hidden-textfield', [ "element" => $element, "data" => $data ]);
                break;

                case "readonly_textfield":

                return view('CMSViews::form.readonly-textfield', [ "element" => $element, "data" => $data ]);
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

                case 'slug':
                  return view('CMSViews::form.slug', [ "element" => $element, "data" => $data ]);
                break;

                case 'tags':
                return view('CMSViews::form.tags', [ "element" => $element, "data" => $data ]);
                break;

                case 'hidden json field' :
                // return "<div class='col-lg-12'><div data-field='".$element->name."' > </div></div>";
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


if(!function_exists('render_form_field')){
    function render_form_field($element , $data , $related_tables = null){

        switch ($element->ui->type) {

                case 'open div': return  '';
                case 'close div': return "";



                case 'file':


                    $file =  \hcolab\cms\models\File::where('name' , $data->{$element->name})->where('deleted',0)->first();

                    if(!$file){ return ""; }

                    if($file->mime_category == "image"){
                        $value = "<a href='".get_media_url($file->name)."' data-fancybox data-type='image'> <img width='100' src='".env('DATA_URL')."/low_resolution/".get_name_from_url($file->name).".jpg' /> </a>";
                    }elseif($file->mime_category == "video"){
                        $value = "<a href='".env('DATA_URL')."/files/original/".$file->name."' data-fancybox data-type='video'> <img width='100' src='".env('DATA_URL')."/low_resolution/".get_name_from_url($file->name).".jpg' /> </a>";

                    }else{
                        $value = "<a href='".env('DATA_URL')."/files/original/".$file->name."' target='blank' > <div style='width:50px;height:50px;display:flex ; align-items : center ; justify-content:center'><i class='file icon' style='color:#c7c5c3;font-size:30px ; height: initial'></i></div>  </a>";



                    }





                    break;
                    case 'multiple file':

                        $files =  \hcolab\cms\models\File::whereIn('name' , json_decode_to_array($data->{$element->name}))->where('deleted',0)->get();

                        if(count($files) < 0){ return ""; }

                        $value = "";
                        foreach($files as $file){
                            if($file->mime_category == "image"){
                                $value .= "<a href='".get_media_url($file->name)."' data-fancybox data-type='image' class='mr-2 mb-2' > <img width='100' src='".env('DATA_URL')."/low_resolution/".get_name_from_url($file->name).".jpg' />";
                            }elseif($file->mime_category == "video"){
                                $value .= "<a href='".env('DATA_URL')."/files/original/".$file->name."' data-fancybox data-type='video' class='mr-2 mb-2'> <img width='100' src='".env('DATA_URL')."/low_resolution/".get_name_from_url($file->name).".jpg' />";
                            }else{
                                $value .= "<a href='".env('DATA_URL')."/files/original/".$file->name."' target='blank' > <div style='width:50px;height:50px;display:flex ; align-items : center ; justify-content:center'><i class='file icon' style='color:#c7c5c3;font-size:30px ; height: initial'></i></div>  </a>";
                            }
                        }
                         break;

                case 'text': return '';

                case "tags":

                    $name = $element->name;

                    if (!property_exists($data, $name)) {
                        $data->$name = [];
                    }

                    if(!$data->$name){
                        $data->$name = [];
                    }

                    $result = "<div>";
                    foreach(json_decode_to_array($data->$name) as $option){
                        $result .=  '<div class="ui grey horizontal label mb-2">'.$option.'</div>';
                    }
                    $result.="</div>";

                    $value = $result;



                break;

                case "multiple select":
                    $name = $element->name;

                    if (!property_exists($data, $name)) {
                        $data->$name = [];
                    }


                    if (isset($related_tables) && !is_null($related_tables) && !empty($related_tables) && isset($related_tables[$name]['data'])) {
                        try {
                            $options = $related_tables[$name]['data'];
                        } catch (\Throwable $th) {
                            $options = collect([]);
                        }

                    }else {
                        try {
                            $options = collect(json_decode(json_encode($element->ui->options)));
                        } catch (\Throwable $th) {
                            $options = collect([]);
                        }
                    }


                    try {
                        $selected = json_decode($data->$name , 1);
                    } catch (\Throwable $th) {
                        $selected = [];
                    }

                    if(!is_array($selected)){ $selected = []; }

                    $options = $options->whereIn('id' , $selected);

                    $result = "<div>";
                    foreach($options as $option){
                        $result .=  '<div class="ui grey horizontal label mb-2">'.$option->label.'</div>';
                    }
                    $result.="</div>";

                    $value = $result;

                case "select":
                    $name = $element->name;
                    if (!property_exists($data, $name)) {
                        $data->$name = '';
                    }

                    if (isset($related_tables) && !is_null($related_tables) && !empty($related_tables) && isset($related_tables[$name]['data'])) {
                        try {
                            $options = $related_tables[$name]['data'];
                        } catch (\Throwable $th) {
                            $options = collect([]);
                        }

                    }else {
                        try {
                            $options = collect(json_decode(json_encode($element->ui->options)));
                        } catch (\Throwable $th) {
                            $options = collect([]);
                        }
                    }

                    $result = $options->where('id', $data->{$element->name} )->first();

                    if($result){
                        $value = $result->label;
                    }

                    break;

                   case 'boolean checkbox':

                    if($data->{$element->name} == 1){
                        $value = "Yes";
                    }else{
                        $value = "No";
                    }

                    break;
            default:


                $value = $data->{$element->name};

            break;


        }
        $label = $element->ui->label;
        // return "
        // <div class='col-lg-12 b-b d-flex'>
        //     <div> ".$label." </div>
        //     <div> ".$value."</div>
        // </div>
        // ";

        return "
        <tr>
            <td style='width:250px'><b> ".$label."</b> </td>
            <td> ".($value ?? '')."</td>
        </tr>
        ";




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
            if(isset($row->id)){
                $array[$row->id] = $row;
            }

            if(isset($row->slug)){
                $array[$row->slug] = $row;
            }
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

    if($type == "optimized-video"){
        return env('DATA_URL')."/files/downloadable-optimized/".$original_name.".mp4";
    }

    if($type == "resized"){
        switch($extension){
            case  "jpg" : return env('DATA_URL')."/files/resized/jpg/".$resized."/".$original_name.".jpg";
            case  "webp" : return env('DATA_URL')."/files/resized/webp/".$resized."/".$original_name.".webp";
            default: return env('DATA_URL')."/files/resized/".$resized."/".$original_name.".".$original_extension;
        }
    }elseif($type == "optimized"){
        switch($extension){
            case  "jpg" : return env('DATA_URL')."/files/optimized/jpg/".$original_name.".jpg";
            case  "webp" : return env('DATA_URL')."/files/optimized/webp/".$original_name.".webp";
            default: return env('DATA_URL')."/files/optimized/".$original_name.".".$original_extension;
        }
    }else{
        return env('DATA_URL')."/files/original/".$original_name.".".$original_extension;
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



if(!function_exists('json_decode_to_array')){
    function json_decode_to_array($payload){

        if(is_array($payload)){
            return $payload;
        }

        try {
            $arr = json_decode($payload);
        } catch (\Throwable $th) {
            $arr = [];
        }
        if(!is_array($arr)){
            return [];
        }
        return $arr;
    }
}

if(!function_exists('query_string_to_array')){
    function query_string_to_array($payload){


            $result = [];
            $arr = explode("&" , $payload);

            foreach($arr as $value){
              $object = explode("=" , $value);
              if(strpos(" ".$object[0] , '%5B%5D')){
                $result[str_replace('%5B%5D' , '' , $object[0])] [] = str_replace('%20' , ' ' , $object[1]);
              }else{
                $result[$object[0]] = str_replace(['%20' , '%3F'] , [' ', '?' ] , $object[1]);
              }

            }


            return $result;
    }
}

if(!function_exists('get_name_initials')){
function get_name_initials($array){
    $str = "";
    foreach($array as $item){
        $str.=$item ? $item[0] : "";
    }
    return $str;
}
}

if(!function_exists('process_menu_item')) {
    function process_menu_item($item)
    {


        if (isset($item['admin']) && $item['admin']) {
            return null;
        }

        switch ($item['type']) {
            case 'page':

                $entity = $item['link_to'];
                $class_exists = class_exists($entity);

                if (!$class_exists) {
                    return null;
                }

                $class = new $entity;

                return [
                    'label' => $class->title,
                    'name' => $class->entity
                ];

            case 'static' :

                return [
                    'label' => $item['label'],
                    'name' => $item['label']
                ];

            default:
                return null;
                break;
        }

        if ($item['type'] == 'page') {

        }

    }
}

    if(!function_exists('replace_template_dictionary')){
        function replace_template_dictionary($array , $dictionary){
                if(!is_array($dictionary) || (is_array($dictionary) && count($dictionary) == 0)){
                    return $array;
                }

                $result = [];
                foreach($array as $array_value){
                    $new_dictionary = [];
                    foreach($dictionary as $key => $value){
                        $new_dictionary [] = "*".$key."*";
                        $new_dictionary [] = "<".$key.">";
                    }

                    $result [] = str_replace($new_dictionary , $value , $array_value);
                }

                return $result;
        }
    }



    if(!function_exists('send_cms_notification')){
        function send_cms_notification($action , $dictionary = [] , $page_slug = null , $row_id = null){

            $template = \hcolab\cms\models\CmsNotificationTemplate::where('action' , $action)->where('deleted',0)->first();
            if(!$template){ return false; }


            $notification = new \hcolab\cms\models\CmsNotification;

            $replace_template_dictionary = replace_template_dictionary([$template->title , $template->description] , $dictionary);

            $notification->title = $replace_template_dictionary[0];
            $notification->description =$replace_template_dictionary[1];
            $notification->read = 0;
            $notification->page_slug = $page_slug;
            $notification->row_id = $row_id;
            $notification->save();

            return true;

        }
    }




    if(!function_exists('send_email_notification')){
        function send_email_notification($email , $action , $dictionary = []){
            try {
                Mail::to($email)->queue(new EmailTemplateMail($action,$dictionary));
                return true;
            } catch (\Throwable $th) {

                dd($th);
                return false;
            }

        }
    }

    if(!function_exists('replace_from_dictionary')){
        function replace_from_dictionary($payload, $dictionary , $wrapper = "*"){
            
            if(!is_array($dictionary)){
                $dictionary = json_decode($dictionary , 1);

                if(!is_array($dictionary)){
                    return $payload;
                }

            }

            if(count($dictionary) == 0){
                return $payload;
            }

            $Search = [];
            $Replace = [];
            foreach ($dictionary AS $key=>$value){
                $Search[]   =   $wrapper.$key.$wrapper;
                $Replace[]  =   $value;
            }

            return str_replace( $Search, $Replace,$payload );

        }
    }




