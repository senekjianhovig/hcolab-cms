<?php
namespace hcolab\cms\pages;
use hcolab\cms\repositories\Page;
        
class CmsThemeBuilderPage extends Page
{
        
    /**
    * Create a new ThemeBuilderPage composer.
    *
    * @return void
    */
    public function __construct()
    {
        $this->version = "1.2";
        $this->page = "CmsThemeBuilderPage";
        $this->entity = "cms_theme_builders";
        $this->slug = "cms-theme-builders";
        $this->title ="Theme Builders";
        $this->sortable = false;
        $this->sort_field = "id";
        $this->sort_direction = "asc"; 
        $this->elements = collect([]);
        $this->columns = collect([]);
        $this->grid_operations = [
            'edit' => ['label' => 'Edit' ,  'link' => '/cms/theme-builder/{id}']  
        ];
    }
        
 
    public function setElements(){


        $config = config('pages');

        $theme_pages = [];
        if(isset($config['theme_pages'])){
            $theme_pages =  $config['theme_pages'];
        }


        return $this
        ->DisabledTextField("ID", "col-lg-12", false , "id", false)
        ->TextField("Label" , "col-lg-4" , true , "label")
        ->Select("Location", "col-lg-4", false, "location" , $theme_pages)
        ->BooleanCheckbox('Publish' , "col-lg-4" , false , "publish")
        ->HiddenJsonField("payload");
    }

    public function setColumns(){
       return $this
       ->Column("id", null , "primary_field")
       ->Column("label")
       ->Column("publish")
       ;  
    }
    
}