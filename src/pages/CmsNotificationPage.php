<?php
namespace hcolab\cms\pages;
use hcolab\cms\repositories\Page;
        
class CmsNotificationPage extends Page
{
        
    /**
    * Create a new SettingPage composer.
    *
    * @return void
    */
    public function __construct()
    {
        $this->version = "1.0";
        $this->page = "CmsNotificationPage";
        $this->entity = "cms_notifications";
        $this->slug = "cms-notifications";
        $this->title ="CMS Notifications";
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
        ->BooleanCheckbox("Read", "col-lg-6", false , "read")
        ->TextAreaField("Description", "col-lg-12", true , "description")
        ->TextField("Page Slug", "col-lg-6", true , "page_slug")
        ->TextField("Row ID", "col-lg-6", true , "row_id");
    }

    public function setColumns(){
       return $this
       ->Column("id", null , "primary_field")
       ->Column("title")
       ->Column("read")
       ->Column("page_slug")
       ->Column('row_id')
       ;  
    }
    
}