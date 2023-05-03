<?php
namespace hcolab\cms\pages;
use hcolab\cms\repositories\Page;
        
class CmsSentPushNotificationPage extends Page
{
        
    /**
    * Create a new SettingPage composer.
    *
    * @return void
    */
    public function __construct()
    {
        $this->version = "1.1";
        $this->page = "CmsSentPushNotificationPage";
        $this->entity = "cms_sent_push_notifications";
        $this->slug = "cms-sent-push-notifications";
        $this->title ="CMS Sent Push Notifications";
        $this->sortable = false;
        $this->sort_field = "id";
        $this->sort_direction = "asc"; 
        $this->elements = collect([]);
        $this->columns = collect([]); 
          
    }
        
 
    public function setElements(){
        return $this
        ->DisabledTextField("ID", "col-lg-12", false , "id", false)
        ->TextField("Device Token", "col-lg-12", true , "device_token")
        ->TextField("Notification ID", "col-lg-12", true , "notification_id")
        ->BooleanCheckbox("Read", "col-lg-12", false , "read")
        ;
    }

    public function setColumns(){
       return $this
       ->Column("id", null , "primary_field")
       ->Column("device_token")
       ->Column("notification_id")
       ->Column("read")
       ;  
    }
    
}