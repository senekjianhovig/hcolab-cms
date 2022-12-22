<?php
namespace hcolab\cms\pages;
use hcolab\cms\repositories\Page;

class CmsUserRolePermissionPage extends Page
{
        
    /**
    * Create a new AlertPage composer.
    *
    * @return void
    */
    public function __construct()
    {
        $this->version = "1.0";
        $this->page = "CmsUserRolePermissionPage";
        $this->entity = "cms_user_role_permissions";
        $this->slug = "cms-user-role-permissions";
        $this->title ="CMS User Role Permissions";
        $this->sortable = false;
        $this->sort_field = "id";
        $this->sort_direction = "asc"; 
        $this->elements = collect([]);
        $this->columns = collect([]);  
        $this->foreign_keys = ["role_id"];         
    }
         
    public function setElements(){
        return $this
        ->DisabledTextField("ID", "col-lg-12", false , "id", false)
        ->ForeignKey("Role", "col-lg-12", true , "role_id")
        ->TextField("Location", "col-lg-12", true , "location")
        ->TextAreaField("Actions", "col-lg-12", true , "actions")
       
        ;
    }

    public function setColumns(){
       return $this
       ->Column("id", null , "primary_field")
       ->Column("location")
       ->Column("actions")
     
       ;  
    }
    
}