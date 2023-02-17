<?php

namespace hcolab\cms\repositories;


class Column 
{

  public $columns;

  public function getColumns(){
  
    $array = [];

    foreach($this->elements as $element){
        $array [$element->name] = $element;
    }

    foreach($this->columns as $column){

      switch ($column->name) {
        case 'created_at':
          $column->type = "date time picker";
          $column->label = "Created";
          break;
          default:
          $column->type = $column->type ? $column->type : $array[$column->name]->ui->type;
          $column->label = $column->label ? $column->label : $array[$column->name]->ui->label;
          break;
      }

    }





    return $this->columns;
  }

  public function Column($name, $label = null , $type = null, $sortable = true, $searchable = true){

    $field_details = collect($this->elements)->where('name' , $name)->first();
    if(!$label){
      $label = $field_details ? $field_details->ui->label : "";
      $type = $field_details ? $field_details->ui->type : "";
    }

    $column = new \StdClass;
    $column->name = $name;
    $column->label = $label;
    $column->type = $type;
    $column->sortable = $sortable;
    $column->searchable = $searchable;
    $column->details = $field_details;
    
    $this->columns = $this->columns->push($column);
    
    return $this;
  }

  public function Section($label , $fields = []){


    $detailed_fields = collect([]);
    
    foreach($fields as $field => $is_editable){
    

      $field_details = collect($this->elements)->where('name' , $field)->first();
      $field_details->is_editable = $is_editable;
      
      if($field_details){
        $detailed_fields->push($field_details);
      }
    }

    $section = new \StdClass;
    $section->title = $label;
    $section->fields = $detailed_fields;

    $this->sections = $this->sections->push($section);

   
    return $this;
  }

}