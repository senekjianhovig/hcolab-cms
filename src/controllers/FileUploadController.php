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
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException;

use Pion\Laravel\ChunkUpload\Handler\DropZoneUploadHandler;


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

        if(request()->debug == 1){
            return $this->responseError(1 , "File could not be uploaded" , "The size you are trying to upload is too big or File extension is not supported!");
        }

        if(!request()->has('file')){
            return $this->responseError(1 , "file is required" , "file is required");
        }



        $file = request()->file('file');
        $temporary = $this->createTemporaryFromFile('temporary_files' , $file);

        if(!$temporary){
            return $this->responseError(1 , "File could not be uploaded" , "The size you are trying to upload is too big or File extension is not supported!");
        }

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

    public function UploadToTemporary(Request $request){







            // dd(HandlerFactory::classFromRequest($request));
            // HandlerFactory::classFromRequest($request)

            // dd(HandlerFactory::classFromRequest($request));
            $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

            if ($receiver->isUploaded() === false) {
                throw new UploadMissingFileException();
            }


            $save = $receiver->receive();




            if ($save->isFinished()) {


                $file = $save->getFile();
                $input_name = request()->input('input_name');
                $is_multiple = request()->has("is_multiple") && (request()->input("is_multiple") == "true" || request()->input("is_multiple") == "1");
                $input_name = str_replace('upld' , 'tmp' ,  $input_name);
                if($is_multiple){ $input_name = $input_name.'[]'; }

                $temporary = $this->createTemporaryFromFile('temporary_files' , $file);

                if(!$temporary){
                    return response()->json([], 404);
                }


                $return = [
                        'value'=> $temporary->name,
                        'name' => $input_name ,
                        'mime_category' => $temporary->mime_category ,
                        'url' =>  env('DATA_URL').'/'.$temporary->url,
                        'display_name' => $temporary->original_name,
                ];

                return view('CMSViews::form.file-preview' , $return);



                // return response()->json([
                //             'value'=> $temporary->name,
                //             'name' => $input_name ,
                //             'mime_category' => $temporary->mime_category ,
                //             'url' =>  env('DATA_URL').'/'.$temporary->url,
                //             'display_name' => $temporary->original_name,

                //             'view' => view('CMSViews::form.file-preview' , [

                //             ])->render()
                // ], 200);


            }

              $handler = $save->handler();


            return response()->json([
                "progress" => $handler->getPercentageDone(),
                'success' => true
            ]);



        // return response()->json([
        //     'success' => false,
        //     'message' => 'File upload failed.',
        // ]);

    }


    public function UploadToTemporaryAPIV2(Request $request){

        ini_set('post_max_size', '500M');



        $uploader_key = request()->header('uploader_key', request()->input('uploader_key' , null));

        if($uploader_key !=  env('UPLOADER_KEY')){
            return $this->responseError(1 , "Wrong Uploader Key" , "Wrong Uploader Key");
        }

        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {


            $file = $save->getFile();
            $temporary = $this->createTemporaryFromFile('temporary_files' , $file);

            if (!$temporary) {
                return $this->responseError(1, "File could not be uploaded", "The size you are trying to upload is too big or File extension is not supported!");
            }

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

        $handler = $save->handler();

        return response()->json([
            "progress" => $handler->getPercentageDone(),
            'success' => true
        ]);

    }

    public function validateFile($file){
        $file_extension = $file->getClientOriginalExtension();
        $mime_type = $file->getMimeType();
        try { $mime_category = explode('/' , $mime_type)[0]; } catch (\Throwable $th) { $mime_category = 'application'; }

        $file_size = $file->getSize();

        $allowed_extensions = [ 'jpg','jpeg','jpe','gif','png', 'bmp', 'tif','tiff','ico','asf','asx','wax','wmv','wmx','avi','divx',
            'flv','mov','qt','mpeg','mpg','mpe','mp4','m4v','ogv','mkv','txt','asc','c','cc','h','csv','tsv','ics','rtx','css','htm','html',
            'mp3m4a','m4b','ra','ram','wav','ogg','oga','mid','midi','wma','mka','rtf','js','pdf','tar','zip','gz','gzip','rar','7z',
            'pot','pps','ppt','doc','wri','xla','xls','xlt','xlw','mdb','mpp','docx','docm','dotx','dotm','xlsx','xlsm','xlsb','xltx',
            'xltm','xlam','pptx','pptm','ppsx','ppsm','potx','potm', 'ppam','sldx','sldm','onetoc','onetoc2','onetmp','onepkg','odt','odp',
            'ods','odg','odc','odb','odf','wp','wpd' , 'svg'
        ];

        if(!in_array(strtolower($file_extension) , $allowed_extensions)){ return false; }

        $bytes_size_per_megabyte = 1048576;
        $max_size = ($mime_category == "video" ? 100 : 5) * $bytes_size_per_megabyte;
        if($file_size > $max_size){ return false; }

        return true;

    }

    public function createTemporaryFromFile($path , $file){

        $disk = env('STORAGE_DISK' , 'public');

        $file_extension = $file->getClientOriginalExtension();

        $mime_type = $file->getMimeType();
        $file_size = $file->getSize();
        $nameWithoutExtension = uniqid().'-'.now()->timestamp;
        $name = $nameWithoutExtension.'.'.$file_extension;

        if(!$this->validateFile($file)){
            return null;
        }

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

            if(!in_array($temporary->extension , ['svg'])){
                $img = Image::make($source)
                    ->resize(300, null, function ($constraint) { $constraint->aspectRatio(); $constraint->upsize(); })
                    ->encode("jpg", 80);
                    $this->saveFromIntervention($img , $jpgResult , $disk);
            }else{

            }

        }elseif($mime_category == "video"){
            $result_video = "low_resolution/".$nameWithoutExtension.".jpg";
            $res = FFMpeg::open($file)
            ->getFrameFromSeconds(2)
            ->export()
            ->toDisk($disk)
            ->withVisibility('public')
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

        $processed = 0;
        if($temporary->mime_category == 'image' && !in_array($temporary->extension , ['svg'])){

            try {
                $this->processImageUpload($temporary , $resize);
                $processed = 1;
            } catch (\Throwable $th) {
                $processed = 0;
            }


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
        $file->url = $external == 1 ? $uri : $original_path;
        $file->resize = $resize;
        $file->external = $external;
        $file->processed = $processed;

        $file->save();

        return $file;
    }


    public function processMediaCron(){

        ini_set('max_execution_time' , 50000);
        ini_set('post_max_size', '500M');

        $nb_ongoing = File::query()->where("process_started" , 1)
        ->where(function($q){
            $q->orWhere("processed" , 0);
            $q->orWhereNull('processed');
        })->where(function($q){
            $q->orWhere("process_error" , 0);
            $q->orWhereNull('process_error');
        })->count();

        if($nb_ongoing > 0){
            return;
        }

        $files = File::query()
        ->where(function($q){
            $q->orWhere("processed" , 0);
            $q->orWhereNull('processed');
        })
        ->where(function($q){
            $q->orWhere("process_started" , 0);
            $q->orWhereNull('process_started');
        })
        ->where(function($q){
            $q->orWhere("process_error" , 0);
            $q->orWhereNull('process_error');
        })
        ->where('deleted',0)
        ->orderBy('id' , 'desc')
        ->get()
        ->take(1);


        foreach($files as $file){
            if(in_array($file->extension , ['svg'])){ continue; }

            switch($file->mime_category){
                case "video" :

                    try {

                        $this->processVideoUpload($file);

                    } catch (\Throwable $th) {
                        $file->process_error = 1;
                        $file->save();
                    }
                    break;
                case "image" :
                    try {

                        $this->processImageUpload($file);

                    } catch (\Throwable $th) {
                        $file->process_error = 1;
                        $file->save();
                    }
                    break;
            }


        }

    }

    public function processVideoUploadOld($file){

        $file->process_started = 1;
        $file->save();

        $input_file = $file->url;
        $file_source = Storage::disk($file->disk)->get("/".$input_file);



        $lowBitrate = 250;
        $midBitrate = 500;
        $highBitrate = 800;
        $superBitrate = 1600;


        try { $this->generateVideoResolution($file , $lowBitrate , 640 , 480); } catch (\Throwable $th) { }
        try { $this->generateVideoResolution($file , $midBitrate , 1280 , 720); } catch (\Throwable $th) { }
        try { $this->generateVideoResolution($file , $highBitrate , 1920 , 1080); } catch (\Throwable $th) { }
        try { $this->generateVideoResolution($file , $superBitrate , 2560 , 1440); } catch (\Throwable $th) { }

        FFMpeg::fromDisk($file->disk)
        ->open("/".$input_file)
        ->exportForHLS()
        ->toDisk($file->disk)
        ->addFormat((new X264)->setKiloBitrate($lowBitrate), function($media) { $media->scale(640, 480); })
        ->addFormat((new X264)->setKiloBitrate($midBitrate), function($media) { $media->scale(1280, 720); })
        ->addFormat((new X264)->setKiloBitrate($highBitrate), function ($media) { $media->scale(1920, 1080); })
        ->addFormat((new X264)->setKiloBitrate($superBitrate), function($media) { $media->scale(2560, 1440); })
        ->save("files/streamable/".get_name_from_url($file->name) . '.m3u8');

        $file->processed = 1;
        $file->save();

    }

    public function processVideoUpload($file){

       $file->process_started = 1;
       $file->save();

        $this->compressVideSameResolution($file);

       $file->processed = 1;
       $file->save();
    }

    public function compressVideSameResolution($file){

        $format = new \FFMpeg\Format\Video\X264('libmp3lame', 'libx264');
        $format->setKiloBitrate(500);

        FFMpeg::fromDisk($file->disk)
        ->open("/".$file->url)
        ->export()
        ->toDisk($file->disk)
        ->inFormat($format)
        ->save("files/downloadable-optimized/".get_name_from_url($file->name) .'.mp4');

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

        $file->process_started = 1;
        $file->save();

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

        $file->processed = 1;
        $file->save();

    }


    public function saveFromIntervention($interventionInstance , $path , $disk = 'public'){
        Storage::disk($disk)->put($path, $interventionInstance->stream() , 'public');
    }


    public function getMedias($file , $force_type = 'array' , $force_optimize = false){

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
                $opt = env('DATA_URL')."/files/downloadable-optimized/".$nameWithoutExtension.".mp4";
            }else{
                $thumbnail = env('DATA_URL').'/files/optimized/jpg/'.$nameWithoutExtension.".jpg";
                $opt = env('DATA_URL').'/files/optimized/jpg/'.$nameWithoutExtension.".jpg";
            }

            $original = env('DATA_URL').'/'.$f->url;

            if($force_optimize){
                $original = $opt;
            }

            return [
                'original_name' => $f->original_name,
                'name' => $f->name,
                'thumbnail' => $thumbnail,
                'display_url' => $original,
                'optimized_url' => $opt,
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
