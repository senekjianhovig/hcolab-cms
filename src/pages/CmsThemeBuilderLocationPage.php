<?php
namespace hcolab\cms\pages;
use hcolab\cms\repositories\Page;
        
class CmsThemeBuilderLocationPage extends Page
{
        
    /**
    * Create a new ThemeBuilderPage composer.
    *
    * @return void
    */
    public function __construct()
    {
        $this->version = "1.2";
        $this->page = "CmsThemeBuilderLocationPage";
        $this->entity = "cms_theme_builder_locations";
        $this->slug = "cms-theme-builder-locations";
        $this->title ="Theme Builder Locations";
        $this->sortable = false;
        $this->sort_field = "id";
        $this->sort_direction = "asc"; 
        $this->elements = collect([]);
        $this->columns = collect([]);
    
    }
        
 
    public function setElements(){

        return $this
        ->DisabledTextField("ID", "col-lg-12", false , "id", false)
        ->TextField("Label" , "col-lg-6" , true , "label")
        ->Slug("Slug", "col-lg-6", false, "slug" , 'label')
        ->TextAreaField("Description" , "col-lg-12" , true , "description")
      
        // ->BooleanCheckbox("Hide", "col-lg-4", false, "hide")
        ;
    }

    public function setColumns(){
       return $this
       ->Column("id", null , "primary_field")
       ->Column("label")
       ->Column("slug")
    //    ->Column('hide')
       ;  
    }
    
}