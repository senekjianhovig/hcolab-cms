<?php
namespace hcolab\cms\pages;
use hcolab\cms\repositories\Page;
        
class CmsPushNotificationPage extends Page
{
        
    /**
    * Create a new SettingPage composer.
    *
    * @return void
    */
    public function __construct()
    {
        $this->version = "1.2";
        $this->page = "CmsPushNotificationPage";
        $this->entity = "cms_push_notifications";
        $this->slug = "cms-push-notifications";
        $this->title ="CMS Push Notifications";
        $this->sortable = false;
        $this->sort_field = "id";
        $this->sort_direction = "asc"; 
        $this->elements = collect([]);
        $this->columns = collect([]); 
        $this->grid_operations = [
            [ 'label' => 'Send Notification'  , 'link' => '/cms/push-notification/{id}/{token}']
        ];              
    }
        
 
    public function setElements(){
        return $this
        ->DisabledTextField("ID", "col-lg-12", false , "id", false)
        ->TextField("Label", "col-lg-12", true , "label")
        
        ->HiddenTextField("Link", "col-lg-12", true , "link")
        ->HiddenTextField("Api", "col-lg-12", true , "api")
        ->HiddenTextField("Page Slug", "col-lg-12", true , "page_slug")

        ->TextField("Title", "col-lg-12", true , "title")
        ->TextAreaField("Message", "col-lg-12", true , "message")
        ->FileUploadField("Image", "col-lg-12", false, "image")
        ->TextAreaField("Text", "col-lg-12", true , "text")
       

        ->TextField("Button Label", "col-lg-12", true , "btn_label")
        ->TextField("Button Link", "col-lg-12", false, "btn_link")
        ;

        
    }

    public function setColumns(){
       return $this
       ->Column("id", null , "primary_field")
       ->Column("label")
       ->Column("title")
       ;  
    }
    
}