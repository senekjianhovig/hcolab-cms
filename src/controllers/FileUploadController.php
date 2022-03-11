<?php

namespace hcolab\cms\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use hcolab\cms\models\TemporaryFile;
use hcolab\cms\models\File;
use Illuminate\Support\Facades\Storage;



class FileUploadController extends Controller
{
    public function UploadToTemporary(){
 
        if(request()->has('file') && request()->has('input_name')){

            $file = request()->file('file');
            $input_name = request()->input('input_name');
            $is_multiple = request()->has("is_multiple") && (request()->input("is_multiple") == "true" || request()->input("is_multiple") == "1");
            $input_name = str_replace('upld' , 'tmp' ,  $input_name);
            if($is_multiple){ $input_name = $input_name.'[]'; }

            $temporary = $this->createTemporaryFromFile('public' , 'temporary_files' , $file);
            
            $file_element = view('CMSViews::form.file-preview', [
                'value'=> $temporary->name,
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
        $name = uniqid().'-'.now()->timestamp.'.'.$file_extension;

        try { $mime_category = explode('/' , $mime_type)[0]; } catch (\Throwable $th) { $mime_category = 'application'; }
        $url = Storage::disk($disk)->putFileAs($path, $file, $name);

        $temporary = new TemporaryFile;
        $temporary->disk = $disk;
        $temporary->path = $path;
        $temporary->name = $name;
        $temporary->original_name = $file->getClientOriginalName();
        $temporary->mime_category =  $mime_category;
        $temporary->mime_type = $mime_type;
        $temporary->extension = $file_extension;
        $temporary->size = $file_size;
        $temporary->url = $url;
        $temporary->save();
        
        return $temporary;
    }

    public function createFileFromTemporary($temporary){

        $input_file = $temporary->url;
        $output_file = str_replace("temporary_files" , "files" , $temporary->url);

        Storage::disk($temporary->disk)->move("/".$input_file,"/".$output_file);

        $file = new File;
        $file->disk = $temporary->disk;
        $file->path = "files";
        $file->name = $temporary->name;
        $file->original_name = $temporary->original_name;
        $file->mime_category =  $temporary->mime_category;
        $file->mime_type = $temporary->mime_type;
        $file->extension = $temporary->extension;
        $file->size =  $temporary->size;
        $file->url = $output_file;
        $file->external = 0;
        $file->save();

        return $file;
    }


}