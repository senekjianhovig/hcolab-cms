<?php

namespace hcolab\cms\imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Illuminate\Support\Facades\DB;

HeadingRowFormatter::default('none');

class GridImport implements ToModel , WithHeadingRow
{

    public $entity;
    public $columns;
    public $primary_field;
    public $related_tables;

    public function __construct($entity , $columns, $primary_field , $related_tables)
    {
        $this->entity = $entity;
        $this->columns = $columns;
        $this->primary_field = $primary_field;
        $this->related_tables = $related_tables;
    }


    public function model(array $row)
    {


        $input = [];

        foreach($this->columns as $column){

            if(!isset($row[$column->label])){
                continue;
            }

            switch($column->type){

                case "select":
                    $value = $row[$column->label];  
                   
                    try {
                        $translated_value = $this->related_tables[$column->name]['data']->where('label' , $value)->first()->id;
                    } catch (\Throwable $th) {
                        $translated_value = null;
                    }
                
                   
                    $input[$column->name] = $translated_value;
                   
                break;
                
                case "multiple select":

                    $value = $row[$column->label]; 
                    $values = explode("," , $value);
                    $tranlated_values = [];
                    foreach($values as $val){
                        $val = trim($val);
                        try {
                            $tranlated_values [] = $this->related_tables[$column->name]['data']->where('label' , $val)->first()->id;
                        } catch (\Throwable $th) {
                           
                        }
                    }

                    $input[$column->name] = json_encode($tranlated_values);

                    
                break;

                case "boolean checkbox": 
                    
                    $value = strtolower($row[$column->label]);

                    if($value == "yes" || $value == "true" || $value == 1 || $value == "1"){
                        $input[$column->name] = 1;
                    }else{
                        $input[$column->name] = 0;
                    }
                
                    break;

                default:

                $input[$column->name] = $row[$column->label];

            }
        }

 
    
        $inputsWithoutID = array_replace([], $input);
        unset($inputsWithoutID["id"]);
    
       // $rows_updated = 0;
       // $rows_created = 0;

        if($this->primary_field && isset($input[$this->primary_field])){

           $row_exist =  DB::table($this->entity)->where('deleted' , 0)->where($this->primary_field , $input[$this->primary_field])->first();
    
           if($row_exist){  
                DB::table($this->entity)->where('deleted' , 0)->where($this->primary_field , $input[$this->primary_field])->update($inputsWithoutID);
             //   $rows_updated++;
            }else{
               $input["deleted"] = 0;
                DB::table($this->entity)->insert($inputsWithoutID);
             //   $rows_created++;
           }
        }else{

            $input["deleted"] = 0;
            DB::table($this->entity)->insert($inputsWithoutID);

         //   $rows_created++;
        }

    
    }
}