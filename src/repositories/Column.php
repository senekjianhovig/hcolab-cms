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

    $column = new \StdClass;
    $column->name = $name;
    $column->label = $label;
    $column->type = $type;
    $column->sortable = $sortable;
    $column->searchable = $searchable;
    
    $this->columns = $this->columns->push($column);
    
    return $this;
}

}