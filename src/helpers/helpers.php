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