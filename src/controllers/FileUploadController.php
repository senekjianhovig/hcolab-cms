<?php

namespace hcolab\cms\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use hcolab\cms\models\TemporaryFile;
use hcolab\cms\models\File;
use Illuminate\Support\Facades\Storage;
use hcolab\cms\traits\ApiTrait;

use Image;

class FileUploadController extends Controller
{

    use ApiTrait;

    public function UploadToTemporaryAPI(){

        if(request()->header('uploader_key') !=  env('UPLOADER_KEY')){
            return $this->responseError(1 , "Wrong Uploader Key" , "Wrong Uploader Key");
        }

        if(!request()->has('file')){
            return $this->responseError(1 , "file is required" , "file is required");
        }

        $file = request()->file('file');
        $temporary = $this->createTemporaryFromFile('public' , 'temporary_files' , $file);
        
        return $this->responseData(1 ,[
            'temporary_id'=> $temporary->id,
            'value'=> $temporary->name.".".$temporary->extension,
            'url' =>  env('APP_URL').'/storage/'.$temporary->url, 
            'display_name' => $temporary->original_name ,
            'mime_category' => $temporary->mime_category,
            'mime_type' => $temporary->mime_type
        ]);
    }

    public function UploadToTemporary(){
 
        if(request()->has('file') && request()->has('input_name')){

            $file = request()->file('file');
            $input_name = request()->input('input_name');
            $is_multiple = request()->has("is_multiple") && (request()->input("is_multiple") == "true" || request()->input("is_multiple") == "1");
            $input_name = str_replace('upld' , 'tmp' ,  $input_name);
            if($is_multiple){ $input_name = $input_name.'[]'; }

            $temporary = $this->createTemporaryFromFile('public' , 'temporary_files' , $file);
            
            $file_element = view('CMSViews::form.file-preview', [
                'value'=> $temporary->name.".".$temporary->extension,
                'name' => $input_name , 
                'mime_category' => $temporary->mime_category , 
                'url' =>  env('APP_URL').'/storage/'.$temporary->url, 
                'display_name' => $temporary->original_name ])->render();

            return response()->json(['file_element'=>$file_element ], 200);
        }
            
        return response()->json([], 404);
    }

    public function createTemporaryFromFile($disk , $path , $file){

        $file_extension = $file->getClientOriginalExtension();
        $mime_type = $file->getClientMimeType(); 
        $file_size = $file->getSize();
        $nameWithoutExtension = uniqid().'-'.now()->timestamp;
        $name = $nameWithoutExtension.'.'.$file_extension;

        try { $mime_category = explode('/' , $mime_type)[0]; } catch (\Throwable $th) { $mime_category = 'application'; }
        $url = Storage::disk($disk)->putFileAs($path, $file, $name);

        $temporary = new TemporaryFile;
        $temporary->disk = $disk;
        $temporary->path = $path;
        $temporary->name = $nameWithoutExtension;
        $temporary->original_name = $file->getClientOriginalName();
        $temporary->mime_category =  $mime_category;
        $temporary->mime_type = $mime_type;
        $temporary->extension = $file_extension;
        $temporary->size = $file_size;
        $temporary->url = $url;
        $temporary->save();
        
        return $temporary;
    }

    public function createFileFromTemporary($temporary , $resize = null){

        
        $input_file = $temporary->url;
    
        $original_path = "files/original/".$temporary->name.".".$temporary->extension;
    
        try {
            Storage::disk($temporary->disk)->copy("/".$input_file, $original_path);
        } catch (\Throwable $th) {
            
        }

        

        if($temporary->mime_category == 'image'){
            $this->processUpload($temporary , $resize);
        }

        $file = File::where('name' , $temporary->name)->where('deleted',0)->first();
        if(!$file){
            $file = new File;
        }
        
        $file->disk = $temporary->disk;
        $file->path = "files";
        $file->name = $temporary->name;
        $file->original_name = $temporary->original_name;
        $file->mime_category =  $temporary->mime_category;
        $file->mime_type = $temporary->mime_type;
        $file->extension = $temporary->extension;
        $file->size =  $temporary->size;
        $file->url = $original_path;
        $file->external = 0;
        $file->save();

        return $file;
    }


    public function processUpload($temporary , $dimension){
       
        $name = $temporary->name;
        $extension = $temporary->extension;
        
        $public_path = storage_path().'/app/public/';
        
        $source = $public_path.$temporary->url;
       
        $main_optimized_directory = "files/optimized/";
        $jpg_optimized_directory = "files/optimized/jpg/";
        $webp_optimized_directory = "files/optimized/webp/";

        $optimized_path = $main_optimized_directory."/".$temporary->name.".".$temporary->extension;
        $optimized_jpg_path = $jpg_optimized_directory ."/".$temporary->name.".jpg";
        $optimized_webp_path = $webp_optimized_directory."/".$temporary->name.".webp";
    
        Storage::disk($temporary->disk)->makeDirectory($main_optimized_directory);
        Storage::disk($temporary->disk)->makeDirectory($jpg_optimized_directory);
        Storage::disk($temporary->disk)->makeDirectory($webp_optimized_directory);

        $IMAGE_OPTIMIZER_MAXWITH = env('IMAGE_OPTIMIZER_MAXWITH' , 2400); 
        $IMAGE_OPTIMIZER_MAXHEIGHT = env('IMAGE_OPTIMIZER_MAXHEIGHT' , 1800); 
        $IMAGE_ENABLE_WEBP = env('IMAGE_ENABLE_WEBP' , 1); 

        $OptimizingImage = Image::make($source);
        $ImageWidth = $OptimizingImage->width();
        $ImageHeight = $OptimizingImage->height();

        if($ImageWidth > $IMAGE_OPTIMIZER_MAXWITH || $ImageHeight > $IMAGE_OPTIMIZER_MAXHEIGHT){
            $height = $ImageWidth <= $ImageHeight ? $IMAGE_OPTIMIZER_MAXHEIGHT : null;
            $width = $ImageWidth > $ImageHeight ? $IMAGE_OPTIMIZER_MAXWITH : null;
            $OptimizingImage->resize($width, $height, function ($constraint) { $constraint->aspectRatio(); });

            $OptimizingImage->encode($temporary->extension, 80)->save($public_path.$optimized_path);
            $OptimizingImage->encode('jpg', 80)->save($public_path.$optimized_jpg_path);
            $OptimizingImage->encode('webp', 80)->save($public_path.$optimized_webp_path);
        }else{
            
            $OptimizingImage->encode($temporary->extension, 80)->save($public_path.$optimized_path);
            $OptimizingImage->encode('jpg', 80)->save($public_path.$optimized_jpg_path);
            $OptimizingImage->encode('webp', 80)->save($public_path.$optimized_webp_path);
        }

        if($dimension != null){
            $main_resize_directory = "files/resized/".$dimension;
            $jpg_resized_directory = "files/resized/jpg/".$dimension;
            $webp_resized_directory = "files/resized/webp/".$dimension;

            $resized_path = $main_resize_directory."/".$temporary->name.".".$temporary->extension;
            $resized_jpg_path = $jpg_resized_directory ."/".$temporary->name.".jpg";
            $resized_webp_path = $webp_resized_directory."/".$temporary->name.".webp";
            
            Storage::disk($temporary->disk)->makeDirectory($main_resize_directory);
            Storage::disk($temporary->disk)->makeDirectory($jpg_resized_directory);
            Storage::disk($temporary->disk)->makeDirectory($webp_resized_directory);
            
            $ResizingImage = Image::make($source);
            $ImageWidth = $ResizingImage->width();
            $ImageHeight = $ResizingImage->height();
           

            $dimension_array = explode('_',$dimension);
            $DIMENSION_WIDTH = $dimension_array[0]; 
            $DIMENSION_HEIGHT = $dimension_array[1];

            $height = $ImageWidth <= $ImageHeight ? $DIMENSION_HEIGHT : null;
            $width = $ImageWidth > $ImageHeight ? $DIMENSION_WIDTH : null;
            $ResizingImage->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });

            $ResizingImage->encode($temporary->extension, 80)->save($public_path.$resized_path);
            $ResizingImage->encode('jpg', 80)->save($public_path.$resized_jpg_path);
            $ResizingImage->encode('webp', 80)->save($public_path.$resized_webp_path);
        }
           
    }


    
        
    
 



}