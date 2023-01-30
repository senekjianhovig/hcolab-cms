<?php



namespace hcolab\cms\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use hcolab\cms\models\TemporaryFile;
use hcolab\cms\models\File;
use Illuminate\Support\Facades\Storage;
use hcolab\cms\traits\ApiTrait;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;

use Image;

class FileUploadController extends Controller
{

    use ApiTrait;

    public function UploadToTemporaryAPI(){

        ini_set('post_max_size', '500M');
  

        $uploader_key = request()->header('uploader_key', request()->input('uploader_key' , null));

        if($uploader_key !=  env('UPLOADER_KEY')){
            return $this->responseError(1 , "Wrong Uploader Key" , "Wrong Uploader Key");
        }

        if(!request()->has('file')){
            return $this->responseError(1 , "file is required" , "file is required");
        }

        $file = request()->file('file');
        $temporary = $this->createTemporaryFromFile('temporary_files' , $file);
        

        return $this->responseData(1 ,[
            'temporary_id'=> $temporary->id,
            'value'=> $temporary->name,
            'url' =>  env('DATA_URL').'/'.$temporary->url, 
            'display_name' => $temporary->original_name ,
            'mime_category' => $temporary->mime_category,
            'mime_type' => $temporary->mime_type,
            'low_resoltion' => $temporary->thumbnail
        ]);
    }

    public function UploadToTemporary(){
 
        if(request()->has('file') && request()->has('input_name')){

            $file = request()->file('file');
            $input_name = request()->input('input_name');
            $is_multiple = request()->has("is_multiple") && (request()->input("is_multiple") == "true" || request()->input("is_multiple") == "1");
            $input_name = str_replace('upld' , 'tmp' ,  $input_name);
            if($is_multiple){ $input_name = $input_name.'[]'; }

            $temporary = $this->createTemporaryFromFile('temporary_files' , $file);
            
            $file_element = view('CMSViews::form.file-preview', [
                'value'=> $temporary->name,
                'name' => $input_name , 
                'mime_category' => $temporary->mime_category , 
                'url' =>  env('DATA_URL').'/'.$temporary->url, 
                'display_name' => $temporary->original_name ])->render();

            return response()->json(['file_element'=>$file_element ], 200);
        }
            
        return response()->json([], 404);
    }

    public function createTemporaryFromFile($path , $file){

        $disk = env('STORAGE_DISK' , 'public');

        $file_extension = $file->getClientOriginalExtension();
        $mime_type = $file->getClientMimeType(); 
        $file_size = $file->getSize();
        $nameWithoutExtension = uniqid().'-'.now()->timestamp;
        $name = $nameWithoutExtension.'.'.$file_extension;

        try { $mime_category = explode('/' , $mime_type)[0]; } catch (\Throwable $th) { $mime_category = 'application'; }
        $url = Storage::disk($disk)->putFileAs($path, $file, $name , 'public');

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
        
        $result = "low_resolution/".$name;
        $jpgResult = "low_resolution/".str_replace([$file_extension], ["jpg"] , $name);
        $public_path = env('STORAGE_DISK') == "public" ? storage_path().'/app/public/' : env('DATA_URL')."/";
        $source = $public_path.$temporary->url;
        
        Storage::disk($disk)->makeDirectory('low_resolution');
       
       
        if($mime_category == "image"){
            
           $img = Image::make($source)
            ->resize(300, null, function ($constraint) { $constraint->aspectRatio(); $constraint->upsize(); })
            ->encode("jpg", 80);
            
            $this->saveFromIntervention($img , $jpgResult , $disk);

        }elseif($mime_category == "video"){
            $result_video = "low_resolution/".$nameWithoutExtension.".jpg";
            $res = FFMpeg::open($file)
            ->getFrameFromSeconds(2)
            ->export()
            ->toDisk($disk)
           // ->withVisibility('public')
            ->save($result_video);
        }

        $temporary->thumbnail =  env('DATA_URL').'/low_resolution/'.$nameWithoutExtension.".jpg";
    
        return $temporary;
    }

    public function createFileFromTemporary($temporary , $resize = null){

        ini_set('max_execution_time' , 50000);
      
        
        $input_file = $temporary->url;

    
        $original_path = "files/original/".$temporary->name;
    
        try {
            Storage::disk($temporary->disk)->copy("/".$input_file, $original_path);
        } catch (\Throwable $th) {
            
        }

        $external = 0;
      
        // if($temporary->mime_category == 'video'){
            
        //     $file_source = Storage::disk($temporary->disk)->get("/".$input_file);

        //     $lowBitrateFormat = (new X264)->setKiloBitrate(500);
        //     $midBitrateFormat  = (new X264)->setKiloBitrate(1500);
        //     $highBitrateFormat = (new X264)->setKiloBitrate(3000);

           
        //     FFMpeg::fromDisk($temporary->disk)
        //         ->open("/".$input_file)
        //         ->addFilter(function ($filters) {
        //             $filters->resize(new Dimension(640, 480));
        //         })
        //         ->export()
        //         ->toDisk($temporary->disk)
        //         ->inFormat($lowBitrateFormat)
        //         ->save("files/downloadable/".get_name_from_url($temporary->name) .'.mp4');


        //         FFMpeg::fromDisk($temporary->disk)
        //         ->open("/".$input_file)
        //         ->exportForHLS()
        //         ->toDisk($temporary->disk)
        //         ->addFormat($lowBitrateFormat)
        //         ->addFormat($midBitrateFormat)
        //         ->addFormat($highBitrateFormat)
        //         ->save("files/streamable/".get_name_from_url($temporary->name) . '.m3u8');
    
        // }


        // if($temporary->mime_category == 'image' && !in_array($temporary->extension , ['svg'])){
        //     $this->processUpload($temporary , $resize);
        // }

        
        $file = File::where('name' , $temporary->name)->where('deleted',0)->first();
        if(!$file){
            $file = new File;
            $file->processed = 0;
        }
        
        $file->disk = $temporary->disk;
        $file->path = "files";
        $file->name = $temporary->name;
        $file->original_name = $temporary->original_name;
        $file->mime_category =  $temporary->mime_category;
        $file->mime_type = $temporary->mime_type;
        $file->extension = $temporary->extension;
        $file->size =  $temporary->size;
        $file->url = $external == 1 ? $uri : $original_path;
        $file->resize = $resize;
        $file->external = $external;
        
        $file->save();

        return $file;
    }


    public function processMediaCron(){

        ini_set('max_execution_time' , 50000);
        ini_set('post_max_size', '500M');

        $files = File::where('processed' , 0)->where('deleted',0)->get();

        foreach($files as $file){
            if(in_array($file->extension , ['svg'])){ continue; }
            
            $file->processed = 1;
            $file->save();

            switch($file->mime_category){
                case "video" :  
                    try {
                        $this->processVideoUpload($file);
                        $file->processed = 1;
                    } catch (\Throwable $th) {
                        $file->processed = 0;
                    }
                    break;
                case "image" :  
                    try {
                        $this->processImageUpload($file); 
                        $file->processed = 1;
                    } catch (\Throwable $th) {
                        $file->processed = 0;
                    }
                    break;
            }

            $file->save();
        }

    }

    public function processVideoUpload($file){

        $input_file = $file->url;
        $file_source = Storage::disk($file->disk)->get("/".$input_file);

       

        $lowBitrate = 250;
        $midBitrate = 500;
        $highBitrate = 800;
        $superBitrate = 1600;

    
        $this->generateVideoResolution($file , $lowBitrate , 640 , 480);
        $this->generateVideoResolution($file , $midBitrate , 1280 , 720);
        $this->generateVideoResolution($file , $highBitrate , 1920 , 1080);
        $this->generateVideoResolution($file , $superBitrate , 2560 , 1440);

        FFMpeg::fromDisk($file->disk)
        ->open("/".$input_file)
        ->exportForHLS()
        ->toDisk($file->disk)
        ->addFormat((new X264)->setKiloBitrate($lowBitrate), function($media) { $media->scale(640, 480); })
        ->addFormat((new X264)->setKiloBitrate($midBitrate), function($media) { $media->scale(1280, 720); })
        ->addFormat((new X264)->setKiloBitrate($highBitrate), function ($media) { $media->scale(1920, 1080); })  
        ->addFormat((new X264)->setKiloBitrate($superBitrate), function($media) { $media->scale(2560, 1440); })
        ->save("files/streamable/".get_name_from_url($file->name) . '.m3u8');

    }


    public function generateVideoResolution($file , $bitrate_value , $w , $h){

        $bitrate = (new X264)->setKiloBitrate($bitrate_value);

        FFMpeg::fromDisk($file->disk)
        ->open("/".$file->url)
        ->addFilter(function ($filters) use($w , $h) {
            $filters->resize(new Dimension($w, $h));
        })
        ->export()
        ->toDisk($file->disk)
        ->inFormat($bitrate)
        ->save("files/downloadable/".$h."p/".get_name_from_url($file->name) .'.mp4');


    }

    public function processImageUpload($file){
       
        $dimension = $file->resize;
        $name = $file->name;
        $extension = $file->extension;
        $nameWithoutExtension = str_replace([$extension , '.'] , ['',''] , $name);
        
        $public_path = env('STORAGE_DISK') == "public" ? storage_path().'/app/public/' : env('DATA_URL')."/";
       
        $source = $public_path.$file->url;
       
        $main_optimized_directory = "files/optimized/";
        $jpg_optimized_directory = "files/optimized/jpg/";
        $webp_optimized_directory = "files/optimized/webp/";

        $optimized_path = $main_optimized_directory."/".$file->name;
        $optimized_jpg_path = $jpg_optimized_directory ."/".$nameWithoutExtension.".jpg";
        $optimized_webp_path = $webp_optimized_directory."/".$nameWithoutExtension.".webp";
    
        Storage::disk($file->disk)->makeDirectory($main_optimized_directory);
        Storage::disk($file->disk)->makeDirectory($jpg_optimized_directory);
        Storage::disk($file->disk)->makeDirectory($webp_optimized_directory);

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

            $this->saveFromIntervention($OptimizingImage->encode($file->extension, 80) , $optimized_path , $file->disk);
            $this->saveFromIntervention($OptimizingImage->encode('jpg', 80) , $optimized_jpg_path , $file->disk);
            $this->saveFromIntervention($OptimizingImage->encode('webp', 80) , $optimized_webp_path , $file->disk);
            
        }else{

            $this->saveFromIntervention($OptimizingImage->encode($file->extension, 80) , $optimized_path , $file->disk);
            $this->saveFromIntervention($OptimizingImage->encode('jpg', 80) , $optimized_jpg_path , $file->disk);
            $this->saveFromIntervention($OptimizingImage->encode('webp', 80) , $optimized_webp_path , $file->disk);

        }

        if($dimension != null){
            $main_resize_directory = "files/resized/".$dimension;
            $jpg_resized_directory = "files/resized/jpg/".$dimension;
            $webp_resized_directory = "files/resized/webp/".$dimension;

            $resized_path = $main_resize_directory."/".$file->name;
            $resized_jpg_path = $jpg_resized_directory ."/".$nameWithoutExtension.".jpg";
            $resized_webp_path = $webp_resized_directory."/".$nameWithoutExtension.".webp";
            
            Storage::disk($file->disk)->makeDirectory($main_resize_directory);
            Storage::disk($file->disk)->makeDirectory($jpg_resized_directory);
            Storage::disk($file->disk)->makeDirectory($webp_resized_directory);
            
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

            $this->saveFromIntervention($ResizingImage->encode($file->extension, 80) , $resized_path , $file->disk);
            $this->saveFromIntervention($ResizingImage->encode('jpg', 80) , $resized_jpg_path , $file->disk);
            $this->saveFromIntervention($ResizingImage->encode('webp', 80) , $resized_webp_path , $file->disk);

        }
           
    }


    public function saveFromIntervention($interventionInstance , $path , $disk = 'public'){ 
        Storage::disk($disk)->put($path, $interventionInstance->stream() , 'public');
    }
    

    public function getMedias($file , $force_type = 'array'){

        $array = [];

        if(is_null($file)){
            return $force_type != "array" ? null : [];
        }


        if($force_type != 'array'){
            $array [] = $file;
        }else{
            $array = $file;
        }
       

        $files = File::whereIn('name' , $array)->where('deleted',0)->get()->map(function($f){

            $nameWithoutExtension = str_replace(['.'.$f->extension] , [''] , $f->name);

            if($f->mime_category == 'video'){
                $thumbnail = env('DATA_URL').'/low_resolution/'.$nameWithoutExtension.".jpg";
            }else{
                $thumbnail = env('DATA_URL').'/files/optimized/jpg/'.$nameWithoutExtension.".jpg";
            }

            $original = env('DATA_URL').'/'.$f->url;

            return [
                'original_name' => $f->original_name,
                'name' => $f->name,
                'thumbnail' => $thumbnail,
                'display_url' => $original,
                'type' => $f->mime_category
            ];
        });

        if($force_type == 'array'){
            return $files;
        }else{
            return count($files) > 0 ? $files[0] : null;
        }
        

    }

}