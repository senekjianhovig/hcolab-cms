<?php

namespace hcolab\cms\repositories;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use hcolab\cms\repositories\ForeignKey;

class Page extends Element
{

    public $version;
    public $page;
    public $entity;
    public $slug;
    public $title;
    public $sortable;
    public $sort_field;
    public $sort_direction;
    public $foreign_keys;
    public $sections;
    public $push_notification_key;
    public $push_notification_page;


    public function checkEntity(){
       
        if (!Schema::hasTable($this->entity)) {
            return false;
        }
       

        $page = DB::table('entity_versions')->where('page', $this->page)->where('deleted',0)->first();
        
        if(!$page){
            return false;
        }
       
        if($this->version != $page->version){
            return false;
        }

        return true;
    }

    public function updateEntity(){
        DB::table('entity_versions')->where('page', $this->page)->where('deleted',0)->update([
            'version' => $this->version
        ]);
    }

    public function generateTable()
    {

        

        if(!$this->checkEntity()){
            
        if (!Schema::hasTable($this->entity)) {
            Schema::create($this->entity, function (Blueprint $table) {
                $table->id();
            });
        }

       

        Schema::table($this->entity, function (Blueprint $table) {
            if (!Schema::hasColumn($this->entity, "created_at")) {
                $table->dateTime('created_at', 0)->useCurrent();
            }
            if (!Schema::hasColumn($this->entity, "updated_at")) {
                $table->dateTime('updated_at', 0)->useCurrent();
            }
            if (!Schema::hasColumn($this->entity, "deleted_at")) {
                $table->softDeletes('deleted_at', 0)->nullable();
            }
            if (!Schema::hasColumn($this->entity, "deleted")) {
                $table->tinyInteger('deleted', 0)->default(0);
            }

        

            if (!Schema::hasColumn($this->entity, "version")) {
                $table->mediumInteger('version')->default(0);
            }
            if (!Schema::hasColumn($this->entity, "orders") && $this->sortable) {
                $table->double("orders", 8, 2)->nullable();
            }

            

            foreach ($this->elements as $element) {
                $field_name = Str::slug($element->name, '_');
                    
                    if (!Schema::hasColumn($this->entity, $field_name) && isset($element->db->field_type)) {
                        switch ($element->db->field_type) {
                            case "varchar":
                                $table->string($field_name, $element->db->field_length)->nullable();
                                break;


                            case "multiple file":
                            case "text":
                                $table->text($field_name)->nullable();
                                break;
                            case "longtext":
                                $table->longText($field_name)->nullable();
                                break;
                            case "tinyint":
                                $table->tinyInteger($field_name)->default(0);
                                break;
                            case "int":
                                $table->integer($field_name)->nullable();
                                break;
                            case "bigint":
                                $table->bigInteger($field_name)->nullable();
                                break;
                            case "decimal":
                                // dd($element->db->field_length, $element->db->field_scale);
                                $table->decimal($field_name, 8 , 2)->nullable();
                            break;
                            case "double":
                                $table->double($field_name, 8, 2)->nullable();
                                break;
                            case "date":
                                $table->date($field_name)->nullable();
                                break;
                            case "datetime":
                                $table->dateTime($field_name, 0)->nullable();
                                break;
                            case "time":
                                $table->time($field_name, 0)->nullable();
                                break;
                            case "image":
                            case "file":
                                $table->string($field_name , 255)->nullable();
                                break;
 
                        }
                    }
             
            }
        });
        
        // $this->updateEntity();
        }
    }


    public function hasPushNotification(){

        if(!(isset($this->push_notification_key) && $this->push_notification_key &&!empty($this->push_notification_key))){
            return false;
        }

        if(!(isset($this->push_notification_page) && $this->push_notification_page &&!empty($this->push_notification_page))){
            return false;
        }
        
        return true;
    }

    public function getRows($pagination = true)
    {
        $table_data = DB::table($this->entity)
        ->where(function($q){
            $q->whereNull('deleted_at');
            $q->orWhere('deleted' , 0);
        });
        
        if (request()->input('sortColumn') && request()->input('sortOrder')) {
            $table_data->orderBy(request()->input('sortColumn'), request()->input('sortOrder'));
        } elseif ($this->sortable) {
           
            // $table_data->orderBy('orders' , $this->sort_direction);
            $table_data->orderBy($this->sort_field, $this->sort_direction);
        }

        $columns = $this->getColumns();


        // dd(request()->all());
        $table_data->where(function ($query) use ($columns) {
            foreach ($columns as $column) {

                if(empty(request()->input('filter_'.$column->name))){
                    continue;
                }
                
               
                
              
                

                $search = request()->input($column->name);
                
                  
                if($column->type == "select"){
                    $query->where($column->name ,$search);
                    continue;
                }else{
                
                    $words = explode(" ", $search);
                    foreach ($words as $word) {
                        $trim_word = trim($word);
                        $searchField = $column->name;
                        $query->where(function ($query1) use ($trim_word, $searchField) {
                            $query1->orWhere($searchField, 'LIKE', $trim_word . '%')
                                ->orWhere($searchField, 'LIKE', '%' . $trim_word . '%')
                                ->orWhere($searchField, 'LIKE', '%' . $trim_word)
                                ->orWhere($searchField, $trim_word);
                        });
                    }
                }
                
            }

            if (request()->input('id')) {
                $query->where('id', request()->input('id'));
            }
        });

        if(!$pagination){
            return $table_data->get();
        }


        $records = request()->input('records' , 20);

        return $table_data->paginate($records);
    }

    public function getRow($id)
    {
        return DB::table($this->entity)
        ->where(function($q){
            $q->whereNull('deleted_at');
            $q->orWhere('deleted' , 0);
          })
        ->where('id', $id)->first();
    }

    public function getRelatedTables()
    {

        $list = ForeignKey::list();
        $results = [];

        if (!is_array($this->foreign_keys)) {
            $this->foreign_keys = [];
        }

        foreach ($this->foreign_keys as $foreign_key) {
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

    public function compareEdit($old_record , $new_record){
      $old = json_decode(json_encode($old_record) , 1);
      $new = json_decode(json_encode($new_record) , 1);

      $updated_fields = [];
      foreach($old as $key => $value){
          if($value != $new[$key]){
              $updated_fields [] = $key;
          } 
      }

      return $updated_fields;
    }

    public function callback($id){
        return false;
    }
}