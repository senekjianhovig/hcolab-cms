<?php
namespace hcolab\cms\pages;
use hcolab\cms\repositories\Page;
        
class CmsSEOPage extends Page
{
        
    /**
    * Create a new SettingPage composer.
    *
    * @return void
    */
    public function __construct()
    {
        $this->version = "1.0";
        $this->page = "CmsSEOPage";
        $this->entity = "cms_seo";
        $this->slug = "cms-seo";
        $this->title ="CMS SEO";
        $this->sortable = false;
        $this->sort_field = "id";
        $this->sort_direction = "asc"; 
        $this->elements = collect([]);
        $this->columns = collect([]);      
    }
        
 
    public function setElements(){
        return $this
        ->DisabledTextField("ID", "col-lg-12", false , "id", false)
        ->HiddenTextField("URL", "col-lg-12", true , "url")
        ->TextField("SEO Title", "col-lg-12", true , "title")
        ->TextAreaField("SEO Description", "col-lg-12", true , "description")
        ->TextAreaField("SEO Keywords", "col-lg-12", false , "keywords");
        
    }

    public function setColumns(){
       return $this
       ->Column("id", null , "primary_field")
       ->Column("url")
       ->Column("title")
       ;  
    }
    
}