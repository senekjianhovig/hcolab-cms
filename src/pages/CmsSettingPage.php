<?php
namespace hcolab\cms\pages;
use hcolab\cms\repositories\Page;
        
class CmsSettingPage extends Page
{
        
    /**
    * Create a new SettingPage composer.
    *
    * @return void
    */
    public function __construct()
    {
        $this->version = "1.0";
        $this->page = "CmsSettingPage";
        $this->entity = "settings";
        $this->slug = "cms-settings";
        $this->title ="CMS Settings";
        $this->sortable = false;
        $this->sort_field = "id";
        $this->sort_direction = "asc"; 
        $this->elements = collect([]);
        $this->columns = collect([]);      
    }
        
 
    public function setElements(){
        return $this
        ->DisabledTextField("ID", "col-lg-12", false , "id", false)
        ->TextField("Key", "col-lg-12", true , "key")
        ->TextField("Group Label", "col-lg-6", true , "group_label")
        ->TextField("Group", "col-lg-6", true , "group")
        ->TextField("Label", "col-lg-6", true , "label")
        ->TextAreaField("Value", "col-lg-6", true , "value")
        ;
    }

    public function setColumns(){
       return $this
       ->Column("id", null , "primary_field")
       ->Column("group_label")
       ->Column("group")
       ->Column("label")
       ->Column("value")
       ;  
    }
    
}