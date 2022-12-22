<?php
namespace hcolab\cms\pages;
use hcolab\cms\repositories\Page;
        
class CmsUserPage extends Page
{
        
    /**
    * Create a new AlertPage composer.
    *
    * @return void
    */
    public function __construct()
    {
        $this->version = "1.0";
        $this->page = "CmsUserPage";
        $this->entity = "cms_users";
        $this->slug = "cms-users";
        $this->title ="CMS Users";
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
        ->TextField("First Name", "col-lg-6", true , "first_name")
        ->TextField("Last Name", "col-lg-6", true , "last_name")
        ->TextField("Email", "col-lg-6", true , "email")
        ->TextField("Phone", "col-lg-6", true , "phone")
        ->PasswordField("Password", "col-lg-12", true , "password")
        ->ForeignKey("Role", "col-lg-12", true , "role_id")
        ;
    }

    public function setColumns(){
       return $this
       ->Column("id", null , "primary_field")
       ->Column("first_name")
       ->Column("last_name")
       ->Column("email")
       ->Column("phone")
       //->Column("role")
       
       
       ;  
    }
    
}