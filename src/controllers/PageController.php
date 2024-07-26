<?php

namespace hcolab\cms\controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use hcolab\cms\exports\GridExport;
use hcolab\cms\imports\GridImport;
use Carbon\Carbon;

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
         'cms-settings' => 'CmsSettingPage',
         'cms-notifications' => 'CmsNotificationPage',
         'cms-notification-templates' => 'CmsNotificationTemplatePage',
         'cms-theme-builders' => 'CmsThemeBuilderPage',
         'cms-theme-builder-sections' => 'CmsThemeBuilderSectionPage',
         'cms-alerts' => 'CmsAlertPage',
         'cms-theme-builder-locations'=>'CmsThemeBuilderLocationPage',
         'cms-seo' => 'CmsSEOPage',
         'cms-push-notifications' => 'CmsPushNotificationPage',
         'cms-sent-push-notifications' => 'CmsSentPushNotificationPage'
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


    public function renderAPI($page_slug){
        $page = $this->initializeRequest($page_slug);
       
        if (is_null($page)) {
            return abort(404);
        }

        $page->setElements();    
        $page->setColumns();

        $rows = $page->getRows(false);
        return response()->json($rows , 200);
    }

    public function render($page_slug , $API = false)
    {

        $page = $this->initializeRequest($page_slug);
       
        if (is_null($page)) {
            return abort(404);
        }

        

        $page->setElements();
    
        $page->setColumns();


        if(isset($page->sections)){
            $page->setSections();
        }
   
 
        
        try {
            $page->generateTable();
        } catch (\Throwable $th) {
            
            return abort(403, "Error Generating Table");
        }

      
        $data["page"] = $page;

     

        if(!$API){
            $data["actions"] = CmsUserRolePermission::getPermissions($page->entity);
        }
      
        if($API){
            return $data;
        }

        return view('CMSViews::page.index_v2', $data);
    }

    public function updatePositions($page_slug){

        $page = $this->initializeRequest($page_slug);
       
        if (is_null($page)) {
            return abort(404);
        }

      
        $positions = json_decode(request()->input('positions'));
        $table_name = $page->entity;
  
    
        foreach($positions as $position){
          DB::table($page->entity)->where('id',$position[0])->update(
            ["orders" => $position[1] ]
        );
        }
  
        return $positions;

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

        $data["actions"] = CmsUserRolePermission::getPermissions($page->entity);

        return response()->json([
            'table_body' => view('CMSViews::grid_v2.grid-body', $data)->render(),
            'pagination' => view('CMSViews::grid_v2.pagination', $data)->render()
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
        $data["actions"] = CmsUserRolePermission::getPermissions($page->entity);
        
        return response()->json([
            'table_body' => view('CMSViews::grid_v2.grid-body', $data)->render(),
            'pagination' => view('CMSViews::grid_v2.pagination', $data)->render()
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
      

        $route_name = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();


        if($route_name == "page.show"){
            $page->setSections();
            $elements = collect($page->sections)->pluck('fields')->flatten()->where('is_editable' , true)->values()->toArray();
        }else{
            $elements = $page->elements;
        }



        $waiting_id_elements = [];

        
        foreach($elements as $element){
            
            // if(!request()->has("tmp_".$element->name) && !request()->has($element->name)){ continue; }

            $value = request()->input($element->name); 
            

            switch ($element->ui->type){
                case "readonly_textfield":
                case "textfield":
                case "select":
                case "disabled_textfield" :
                case "hidden_textfield" : 
                case "textarea":
                case "url":
                case "slug": 
                case "wysiwyg":
                {
                    // if($value == null){ break; }
                    $inputs[$element->db->field_name] = $value;
                    break;
                }
                case "password":
                {
                    // if($value == null || empty($value)){ break; }
                    $inputs[$element->db->field_name] = Hash::make(request()->input($element->db->field_name));
                    break;
                }
                case "date time picker":
                case "date picker":
                {

                    // if($value == null){ break; }
                    $value = date("Y-m-d H:i:s",strtotime($value));
                    $inputs[$element->db->field_name] = $value;
                    break;
                }
                case "tags":
                case "multiple select":
                {
                    // if($value == null){ break; }
                    $inputs[$element->name] = json_encode(request()->input($element->name));
                    break;
                }

                case "values select":
                {
                    // if($value == null){ break; }
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
                    // if($value == null){ break; }
                    $inputs[$element->db->field_name] = request()->has($element->db->field_name) ? request()->input($element->db->field_name) : 0;
                    break;
                }

                case "hidden json field":
                {
                    // if($value == null){ break; }
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
            $old_record = DB::table($page->entity)->where('id',request()->input('id'))->first();
            DB::table($page->entity)->where('id',request()->input('id'))->update($inputs);
            $new_record = DB::table($page->entity)->where('id',request()->input('id'))->first();
            $new_entry = false;
        }else{
            $id = DB::table($page->entity)->insertGetId($inputs);
            $new_entry = true;
        }
       

        try {
            $updated_fields = !$new_entry ? $page->compareEdit($old_record , $new_record) : null;
            $page->callback($id, $updated_fields);
        } catch (\Throwable $th) {
            //throw $th;
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


    public function validateSlug($page_slug , $key ,$slug){
        $page = $this->initializeRequest($page_slug);
        if (is_null($page)) {
            return abort(404);
        }

        if(method_exists($page , 'validateSlug')){
            return $page->validateSlug($slug);
        }else{

          $record =  DB::table($page->entity)->where($key , $slug)->where('deleted',0)->first();

          if($record){
              return response()->json(0, 200);
          }else{
              return response()->json(1, 200);
          }

        }
       
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

        $page = $this->initializeRequest($page_slug);
        if (is_null($page)) {
            return abort(404);
        }

        $check = CmsUserRolePermission::checkPermissions($page->entity , 'update');
        if(!$check){ return abort(404); }


        $notification = \hcolab\cms\models\CmsNotification::where('deleted',0)->where('row_id', $id)->where('page_slug' , $page->slug)->first();
        if($notification){
            $notification->read = 1;
            $notification->save();
        }




        $page->setElements();

        try {
            $page->setSections();
        } catch (\Throwable $th) {
            //throw $th;
        }

        
        try {
            $page->generateTable();
        } catch (\Throwable $th) {
            return abort(403, "Error Generating Table");
        }

        $page->getRow($id);

        $data["page"] = $page;

        $data["data"] = $page->getRow($id);
        $data["id"] = $id;

        

        return view('CMSViews::page.show' , $data);
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

    public function import($page_slug){

        

        $page = $this->initializeRequest($page_slug);
       
        if (is_null($page)) {
            return abort(404);
        }

        $check = CmsUserRolePermission::checkPermissions($page->entity , 'export');
        if(!$check){ return abort(404); }


        $page->setElements();
    
        $page->setColumns();

        if(isset($page->sections)){
            $page->setSections();
        }

        request()->validate([
            'upload_file' => 'required'
        ]);

        $file = request()->file('upload_file');
        $primary_field =  request()->input('primary_field');

        $related_tables = $page->getRelatedTables();
        $columns = $page->getExportColumns();
        

        ini_set('max_execution_time', 180);

        // dd($related_tables);

        Excel::import(new GridImport($page->entity , $columns ,$primary_field , $related_tables), $file);

        return redirect()->route('page' , ['page_slug' => $page_slug , "notification_type"=>"success"  , "notification_message"=> "Import successfully completed!"]);

    }

    public function renderImport($page_slug){
       
        $page = $this->initializeRequest($page_slug);
       
        if (is_null($page)) {
            return abort(404);
        }

        $check = CmsUserRolePermission::checkPermissions($page->entity , 'import');
        if(!$check){ return abort(404); }


        $page->setElements();
    
        $page->setColumns();

        if(isset($page->sections)){
            $page->setSections();
        }
   
   
        $data["page"] = $page;

        return view('CMSViews::page.superimport' , $data);
    }

    public function export($page_slug){
        $page = $this->initializeRequest($page_slug);
        if (is_null($page)) {
            return response()->json([], 404);
        }

     
        $check = CmsUserRolePermission::checkPermissions($page->entity , 'export');
        if(!$check){ return abort(404); }

        $page->setElements();
        $page->setColumns();
        $rows = $page->getRows(false);

        $related_tables = $page->getRelatedTables();
        $columns = $page->getExportColumns();

        $rows = collect($rows)->map(function($row) use ($columns , $related_tables){
            $result = [];

            foreach($columns as $column){
                $result[$column->name] = process_grid_field($row , $column , $related_tables , false);
            }

            return $result;
        });

    
        $columns = collect($columns)->pluck('label')->values()->toArray();

        return Excel::download(new GridExport($rows , $columns), $page->entity.'_'.Carbon::now()->format('d-m-Y h:i:s').'.xlsx');

    }

}