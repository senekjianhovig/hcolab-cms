<?php

namespace hcolab\cms\repositories;
use Illuminate\Support\Facades\DB;

class ForeignKey
{

public static function list(){

    $array = [];

    $foreign_keys = config('pages')['foreign_keys'];

  

    foreach($foreign_keys as $foreign_key){
        $name = $foreign_key['name'];
        $format = explode(',' , $foreign_key['format']);
       
        switch ($foreign_key['type']) {
            case 'single':
                $first = explode(':' , $format[0]);
                $array[$name] = self::singleTableQuery( $first[0], $first[1], $first[2] , $foreign_key['options'] ?? []);
            break;
            
            case 'double':
                $first = explode(':' , $format[2]);
                $second = explode(':' , $format[0]);
                $array[$name] =  self::doubleTableQuery($first[0], $second[0], $first[2] , $second[2] , $first[1], $second[1], $format[1]);
            break;
            
            default:
            break;
        }
       
    }
    return $array;

    // return [
        // 'subcategory_id' => self::doubleTableQuery('subcategories', 'categories', 'label' , 'label' , 'id' ,'category_id', '/'),
        // 'category_id' => self::singleTableQuery('categories', 'id', 'label'),
        // 'country_id' => self::singleTableQuery('countries', 'id', 'name')
    // ];
}

public static function singleTableQuery($table , $key , $value , $options = []){
    $result =  DB::table($table)->select($key.' as id', $value.' as label')->whereNotNull($value)->where('deleted',0);
		foreach($options as $optionkey=>$option){
			$result->where($optionkey , $option);
		}

return $result;

}

public static function doubleTableQuery($main_table, $related_table,  $main_table_value , $related_table_value , $main_key, $foreign_key, $seperator = '/'){
   
   
    // dd($main_table, $related_table,  $main_table_value , $related_table_value , $main_key, $foreign_key, $seperator);
    return DB::table($main_table)->select($main_table.'.id')
    ->selectRaw("CONCAT(".$related_table.".".$related_table_value.", ' ". $seperator ." '  , ".$main_table.".".$main_table_value.") as label")
    ->where($main_table.'.deleted',0)
    ->join($related_table, $related_table.'.'.$main_key, $main_table.'.'.$foreign_key);
}

public static function tripleTableQuery(){
       
}

public static function getRelatedTables($foreign_keys)
{

    $list = self::list();
    $results = [];

    if (!is_array($foreign_keys)) {
        $foreign_keys = [];
    }

    foreach ($foreign_keys as $foreign_key) {
        if (array_key_exists($foreign_key, $list)) {

            $data = $list[$foreign_key]->get();
            $results[$foreign_key] = [
                'data' => $data,
                'indexed_data' => set_id_index($data)
            ];
        }
    }

    return $results;
}


}
