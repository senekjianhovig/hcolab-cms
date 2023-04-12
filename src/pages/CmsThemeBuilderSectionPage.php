<?php
namespace hcolab\cms\pages;
use hcolab\cms\repositories\Page;
        
class CmsThemeBuilderSectionPage extends Page
{
        
    /**
    * Create a new ThemeBuilderPage composer.
    *
    * @return void
    */
    public function __construct()
    {
        $this->version = "1.2";
        $this->page = "CmsThemeBuilderSectionPage";
        $this->entity = "cms_theme_builder_sections";
        $this->slug = "cms-theme-builder-sections";
        $this->title ="Theme Builders Sections";
        $this->sortable = true;
        $this->sort_field = "orders";
        $this->sort_direction = "asc"; 
        $this->elements = collect([]);
        $this->columns = collect([]);
    }
        
 
    public function setElements(){
        return $this
        ->DisabledTextField("ID", "col-lg-12", false , "id", false)
        ->TextField("Name" , "col-lg-6" , true , "name")
        ->TextField("Title" , "col-lg-6" , true , "title")
        ->ForeignKey("Theme Builder" , "col-lg-12" , true , "theme_builder_id")
        ->HiddenJsonField("payload");
    }

    public function setColumns(){
       return $this
       ->Column("id", null , "primary_field")
       ->Column("name")
       ->Column("title")
       ;  
    }
    
}