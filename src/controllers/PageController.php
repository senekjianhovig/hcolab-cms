<?php

namespace hcolab\cms\controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class PageController extends Controller
{

   

    public function initializeRequest($page_slug)
    {
       
        $class_name = $this->getPageFromSlug($page_slug);
        $class = "\\App\\Pages\\" . $class_name;

        try {
            return new $class;
        } catch (\Throwable $th) {
            return null;
        }
    }


    public function render($page_slug)
    {

        
        $page = $this->initializeRequest($page_slug);
        if (is_null($page)) {
            return abort(404);
        }

        

        $page->setElements();
    
        $page->setColumns();


        try {
            $page->generateTable();
        } catch (\Throwable $th) {
            return abort(403, "Error Generating Table");
        }

        $data["page"] = $page;


        return view('CMSViews::page.index', $data);
    }

    public function query($page_slug)
    {
        $page = $this->initializeRequest($page_slug);
        if (is_null($page)) {
            return response()->json([], 404);
        }

        $page->setElements();
        $page->setColumns();

        $data["page"] = $page;

        return response()->json([
            'table_body' => view('cms.grid.grid-body', $data)->render(),
            'pagination' => view('cms.grid.pagination', $data)->render()
        ], 200);
    }

    public function save($page_slug){
        
        $page = $this->initializeRequest($page_slug);
        if (is_null($page)) {
            return abort(404);
        }

        $page->setElements();
        
        $inputs = [];
        dd(request()->all());
        
        foreach($page->elements as $element){
          
           
            switch ($element->ui->type){
                case "textfield":
                case "select":
                case "disabled_textfield" :
                case "hidden_textfield" : 
                case "textarea":
                case "url":
                case "wysiwyg":
                {
                    $inputs[$element->db->field_name] = request()->input($element->db->field_name);
                    break;
                }
                case "password":
                {
                    $inputs[$element->db->field_name] = Hash::make(request()->input($element->db->field_name));
                    break;
                }
                case "date time picker":
                case "date picker":
                {
                    $inputs[$element->db->field_name] = date("Y-m-d H:i:s",strtotime(request()->input($element->db->field_name)));
                    break;
                }
                case "multiple select":
                {
                    $inputs[$element->name] = json_encode(request()->input($element->name));
                    break;
                }
                case "multiple file":
                {
                    $inputs[$element->name] = request()->has('upld_'.$element->name.'[]') ? json_encode(request()->input('upld_'.$element->name.'[]')) : json_encode([]);
                    break;
                }

                
                case "boolean checkbox":
                {
                    $inputs[$element->db->field_name] = request()->has($element->db->field_name) ? request()->input($element->db->field_name) : 0;
                    break;
                }
                case "image":
                case "file":
                {
                    $inputs[$element->name] =  request()->input('upld_'.$element->name);
                    break;
                }
                default: 
                break;
            }
        }
        
        dd($inputs);

        $id = DB::table($admin_table->name)->insertGetId($inputs);
       

    }

    public function create($page_slug)
    {

        $page = $this->initializeRequest($page_slug);
        if (is_null($page)) {
            return abort(404);
        }

        $page->setElements();

        try {
            $page->generateTable();
        } catch (\Throwable $th) {
            dd($th);
            return abort(403, "Error Generating Table");
        }

        $data["page"] = $page;



        return view('CMSViews::page.form', $data);
    }



    public function edit($page_slug, $id)
    {

        $page = $this->initializeRequest($page_slug);
        if (is_null($page)) {
            return abort(404);
        }

        $page->setElements();

        try {
            $page->generateTable();
        } catch (\Throwable $th) {
            return abort(403, "Error Generating Table");
        }

        $page->getRow($id);

        $data["page"] = $page;

        $data["data"] = $page->getRow($id);
        $data["id"] = $id;

        return view('CMSViews::page.form', $data);
    }

    public function show($page_slug, $id)
    {
        return view('CMSViews::page.show');
    }

    public function getPageFromSlug($slug)
    {
        $pages = $this->mapSlugToPage();

        if (!array_key_exists($slug, $pages)) {
            return false;
        }

        return $pages[$slug];
    }

    public function mapSlugToPage()
    {
        $pages = [];
        $path = app_path()."/Pages/";
        $files = File::files($path);

        foreach($files as $file){
            $file_name = $file->getFilename();
            $newFileName = str_replace('.php' , '' , $file_name);
            $pages[get_page_settings($newFileName)['slug']] = $newFileName; 
        }

        return $pages;
    }
}