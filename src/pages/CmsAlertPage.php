<?php
namespace hcolab\cms\pages;
use hcolab\cms\repositories\Page;
        
class CmsAlertPage extends Page
{
        
    /**
    * Create a new SettingPage composer.
    *
    * @return void
    */
    public function __construct()
    {
        $this->version = "1.0";
        $this->page = "CmsAlertPage";
        $this->entity = "cms_alerts";
        $this->slug = "cms-alerts";
        $this->title ="CMS Alerts";
        $this->sortable = false;
        $this->sort_field = "id";
        $this->sort_direction = "asc"; 
        $this->elements = collect([]);
        $this->columns = collect([]);      
    }
        
 
    public function setElements(){
        return $this
        ->DisabledTextField("ID", "col-lg-12", false , "id", false)
        ->TextField("Title", "col-lg-6", true , "title")
        ->TextField("Key", "col-lg-6", true , "key")
        ->TextAreaField("Message", "col-lg-12", true , "message")
        ->Select("Type", "col-lg-12", true , "type" , [
            ['id' => 'error' , 'label' => 'Error'],
            ['id' => 'success' , 'label' => 'Success']
        ]);
       
     
    }

    public function setColumns(){
       return $this
       ->Column("id", null , "primary_field")
       ->Column("title")
       ->Column("key")
       ->Column("type")
       ;  
    }
    
}