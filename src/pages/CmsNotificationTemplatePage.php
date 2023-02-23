<?php
namespace hcolab\cms\pages;
use hcolab\cms\repositories\Page;
        
class CmsNotificationTemplatePage extends Page
{
        
    /**
    * Create a new SettingPage composer.
    *
    * @return void
    */
    public function __construct()
    {
        $this->version = "1.0";
        $this->page = "CmsNotificationTemplatePage";
        $this->entity = "cms_notification_templates";
        $this->slug = "cms-notification-templates";
        $this->title ="CMS Notification Templates";
        $this->sortable = false;
        $this->sort_field = "id";
        $this->sort_direction = "asc"; 
        $this->elements = collect([]);
        $this->columns = collect([]);      
    }
        
 
    public function setElements(){
        return $this
        ->DisabledTextField("ID", "col-lg-12", false , "id", false)
        ->TextField("Action", "col-lg-12", false , "action")
        ->TextField("Dictionary", "col-lg-12", false , "dictionary")
        ->TextField("Title", "col-lg-12", true , "title")
        ->TextAreaField("Description", "col-lg-12", true , "description")
     ;
        
    }

    public function setColumns(){
       return $this
       ->Column("id", null , "primary_field")
       ->Column("title")
       ->Column("description")
       ->Column("action")
       ;  
    }
    
}