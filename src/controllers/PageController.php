<?php

namespace hcolab\cms\controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use hcolab\cms\models\File as FileModel;
use hcolab\cms\models\TemporaryFile as TemporaryFileModel;
use Illuminate\Support\Facades\DB;
use hcolab\cms\models\CmsUserRolePermission;

class PageController extends Controller
{

   private $custom_pages;

   public function __construct(){
       $this->custom_pages = [
         'cms-users' => 'CmsUserPage',
         'cms-user-roles' => 'CmsUserRolePage',
         'cms-user-role-permissions' => 'CmsUserRolePermissionPage',
         'cms-settings' => 'CmsSettingPage'
       ];
   }

    public function initializeRequest($page_slug)
    {
     
        $namespace ="\\App\\Pages\\";
        if(strpos(" ".$page_slug , "cms") > 0){
            $namespace = "\\hcolab\\cms\\pages\\";
        }

        $class_name = $this->getPageFromSlug($page_slug);
        
        $class = $namespace . $class_name;

       
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


        $data["actions"] = CmsUserRolePermission::getPermissions($page->entity);

      

        return view('CMSViews::page.index', $data);
    }

    public function query($page_slug)
    {

       
        $page = $this->initializeRequest($page_slug);
        if (is_null($page)) {
            return response()->json([], 404);
        }

        $check = CmsUserRolePermission::checkPermissions($page->entity , 'read');
        if(!$check){ return abort(404); }

        $page->setElements();
        $page->setColumns();

        $data["page"] = $page;

        return response()->json([
            'table_body' => view('CMSViews::grid.grid-body', $data)->render(),
            'pagination' => view('CMSViews::grid.pagination', $data)->render()
        ], 200);
    }

    public function delete($page_slug , $id){
       

        $page = $this->initializeRequest($page_slug);
        if (is_null($page)) {
            return response()->json([], 404);
        }

        $check = CmsUserRolePermission::checkPermissions($page->entity , 'delete');
        if(!$check){ return response()->json([], 404); }

        $page->setElements();
        $page->setColumns();

        DB::table($page->entity)->where('id' , $id)->update(['deleted' => 1]);

        $data["page"] = $page;

        return response()->json([
            'table_body' => view('CMSViews::grid.grid-body', $data)->render(),
            'pagination' => view('CMSViews::grid.pagination', $data)->render()
        ], 200);

    }


    public function save($page_slug){
        $page = $this->initializeRequest($page_slug);
        if (is_null($page)) {
            return abort(404);
        }

        try {
         
            DB::transaction(function () use($page, $page_slug) {

       
        
        $page->setElements();
        $inputs = [];
      
        $waiting_id_elements = [];


     
        
        foreach($page->elements as $element){
        
            $value = request()->input($element->name); 
            

            switch ($element->ui->type){
                case "readonly_textfield":
                case "textfield":
                case "select":
                case "disabled_textfield" :
                case "hidden_textfield" : 
                case "textarea":
                case "url":
                case "wysiwyg":
                {
                    if($value == null){ break; }
                    $inputs[$element->db->field_name] = $value;
                    break;
                }
                case "password":
                {
                    if($value == null || empty($value)){ break; }
                    $inputs[$element->db->field_name] = Hash::make(request()->input($element->db->field_name));
                    break;
                }
                case "date time picker":
                case "date picker":
                {

                    if($value == null){ break; }
                    $value = date("Y-m-d H:i:s",strtotime($value));
                    $inputs[$element->db->field_name] = $value;
                    break;
                }
                case "tags":
                case "multiple select":
                {
                    if($value == null){ break; }
                    $inputs[$element->name] = json_encode(request()->input($element->name));
                    break;
                }

                case "values select":
                {
                    if($value == null){ break; }
                    $inputs[$element->name] = request()->input($element->name);
                    break;
                }
                case "multiple file":
                {
                   
                    $old_files = [];
                    if(request()->has($element->name)){
                        $old_files = request()->input($element->name);
                    }
                   
                    $new_files = [];
                   
                    
                    if(request()->has('tmp_'.$element->name)){
                       
                        

                        $temp_files = TemporaryFileModel::whereIn('name' , request()->input('tmp_'.$element->name) )->where('deleted',0)->get();
                        foreach($temp_files as $temporary){

                            

                            $file = (new FileUploadController)->createFileFromTemporary($temporary , isset($element->ui->resize) ? $element->ui->resize : null);
                            $new_files [] = $file->name;
                        }
                       
                    }
                    
                    //merge
                    $files = array_merge($old_files , $new_files);


                    $inputs[$element->name] = json_encode($files);
                    break;
                }

                
                case "boolean checkbox":
                {
                    if($value == null){ break; }
                    $inputs[$element->db->field_name] = request()->has($element->db->field_name) ? request()->input($element->db->field_name) : 0;
                    break;
                }

                case "hidden json field":
                {
                    if($value == null){ break; }
                    $inputs[$element->db->field_name] = request()->has($element->db->field_name) ? json_encode(request()->input($element->db->field_name)) : null;
                break;
                }

                case "image":
                case "file":
                {
                    

                    if(request()->has('tmp_'.$element->name)){
                        $new_files = [];
                   
                        $temporary = TemporaryFileModel::where('name' , request()->input('tmp_'.$element->name) )->where('deleted',0)->first();
                        $file = (new FileUploadController)->createFileFromTemporary($temporary, $element->ui->resize);            
                        $inputs[$element->name] = $file->name;
                    }elseif(request()->has($element->name)){
                        $inputs[$element->name] =  request()->input($element->name);
                    }else{
                        $inputs[$element->name] =  null;
                    }

                   
                    break;
                }

                case "variants panel":

                    $waiting_id_elements [] = $element;

                break;

                default:
                
                break;


                


            }
        }
        

       

        if(request()->has('id') && !empty(request()->input('id'))){
            $id = request()->input('id');
            DB::table($page->entity)->where('id',request()->input('id'))->update($inputs);
        }else{
            $id = DB::table($page->entity)->insertGetId($inputs);
        }
       

        

        if(count($waiting_id_elements) > 0){

            foreach($waiting_id_elements as $waiting_id_element){

                switch ($waiting_id_element->ui->type) {
                    case "variants panel":
                        
                        $stock_quantity_arr = request()->input('stock_quantity');
                        $price_arr = request()->input('price');
                        $discount_arr = request()->input('discount');
                        $cost_arr = request()->input('cost');
                        $charge_tax = request()->input('charge_tax') == "on";
                        $variant_arr = request()->input('variant');
                        $include_variant = request()->input('include_variant');
                        $count_variants = count($variant_arr);

                        // Initialize Pages
                        $target_page = new $waiting_id_element->ui->target_page;
                        $variant_page = new $waiting_id_element->ui->variant_page;
                        $product_price_page = new $waiting_id_element->ui->product_price_page;
                        $product_inventory_page = new $waiting_id_element->ui->product_inventory_page;

                        $products = $target_page->getProductsByGroupID($id);
                        $prefix_arr = $variant_page->getIDPrefixes();

                      

                            for($i=0; $i<$count_variants ; $i++){
                                
                                $check = $include_variant == "on" || !in_array('no-variant',$variant_arr);

                                if($variant_arr[$i] == "no-variant" && $check){
                                    continue;
                                }
                                
                                
                               
                                if(array_key_exists($variant_arr[$i] , $products)){
                                    $sku_arr = request()->input('sku');
                                    $barcode_arr = request()->input('barcode');
                                    $hide_arr = request()->input('hide_product');
                                    $image_arr = request()->input('image');
                                    if(!is_array($hide_arr)){ $hide_arr = []; }
                                    $product_id =  $target_page->updateProduct($products[$variant_arr[$i]], $id ,$image_arr[$i] ,$stock_quantity_arr[$i] , $sku_arr[$i] , $barcode_arr[$i] , in_array($variant_arr[$i], $hide_arr) ? 1 : 0);
                                }else{
                                    $product_id =  $target_page->createProduct($id, $variant_arr[$i] , $stock_quantity_arr[$i] , $prefix_arr);
                                }

                                $product_price_page->createPrice($product_id, $price_arr[$i] , $discount_arr[$i] , $cost_arr[$i] , $charge_tax );
                                $product_inventory_page->createInventory($product_id, $stock_quantity_arr[$i]);

                                if(!$check){ break; }
                            }
                       

    
                    break;
                    default:
                    break;
                }

            }

        }


       

        });

        if(request()->has('redirect') && !is_null(request()->input('redirect'))){
            return redirect(request()->input('redirect'));
        }
      
        return redirect(env('APP_URL').'/cms/page/'.$page->slug);
    
    } catch (\Throwable $th) {
        dd($th);
    }
    }

    public function create($page_slug)
    {

        $page = $this->initializeRequest($page_slug);
        if (is_null($page)) {
            return abort(404);
        }

        $check = CmsUserRolePermission::checkPermissions($page->entity , 'create');
        if(!$check){ return abort(404); }

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

        $check = CmsUserRolePermission::checkPermissions($page->entity , 'update');
        if(!$check){ return abort(404); }

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

        if(isset($this->custom_pages) && is_array($this->custom_pages)){
            foreach($this->custom_pages as $slug => $class){
                $pages[$slug] = $class;
            }
        }
  
       
        return $pages;
    }
}