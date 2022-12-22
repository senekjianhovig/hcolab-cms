<?php
namespace hcolab\cms\pages;
use hcolab\cms\repositories\Page;
        
class CmsUserRolePage extends Page
{
        
    /**
    * Create a new AlertPage composer.
    *
    * @return void
    */
    public function __construct()
    {
        $this->version = "1.0";
        $this->page = "CmsUserRolePage";
        $this->entity = "cms_user_roles";
        $this->slug = "cms-user-roles";
        $this->title ="CMS User Roles";
        $this->sortable = false;
        $this->sort_field = "id";
        $this->sort_direction = "asc"; 
        $this->elements = collect([]);
        $this->columns = collect([]);  
        $this->grid_operations = [
            [ 'label' => 'Permissions'  , 'link' => '/cms/role-permissions/{id}/{token}']
        ];     
    }
         
    public function setElements(){
        return $this
        ->DisabledTextField("ID", "col-lg-12", false , "id", false)
        ->TextField("Label", "col-lg-12", true , "label")
       
        ;
    }

    public function setColumns(){
       return $this
       ->Column("id", null , "primary_field")
       ->Column("label")
     
       ;  
    }
    
}