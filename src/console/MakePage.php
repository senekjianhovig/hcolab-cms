<?php

namespace hcolab\cms\console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;

class MakePage extends Command
{

    protected $signature = 'make:page {page}';

    protected $files;
 
    protected $description = 'Generate a page';


    public function __construct(Filesystem $files)
    {
        $this->files=$files;
        parent::__construct();
    }

  
    public function handle()
    {
        $page=$this->argument('page');

        if ($page === '' || is_null($page) || empty($page)) {
            return $this->error('Page Name Invalid..!');
        }

        $settings= get_page_settings($page);
        $entity = $settings['entity'];
        $slug = $settings['slug'];
        $title = $settings['title'];

$contents=
'<?php
namespace App\Pages;
use hcolab\cms\repositories\Page;
        
class '.$page.' extends Page
{
        
    /**
    * Create a new '.$page.' composer.
    *
    * @return void
    */
    public function __construct()
    {
        $this->version = "1.0";
        $this->page = "'.$page.'";
        $this->entity = "'.$entity.'";
        $this->slug = "'.$slug.'";
        $this->title ="'.$title.'";
        $this->sortable = false;
        $this->sort_field = "id";
        $this->sort_direction = "asc"; 
        $this->elements = collect([]);
        $this->columns = collect([]);      
    }
        
 
    public function setElements(){
        return $this
        ->DisabledTextField("ID", "col-lg-12", false , "id", false);
    }

    public function setColumns(){
       return $this
       ->Column("id", null , "primary_field");  
    }
    
}';

        $file = "${page}.php";
        $path=app_path();
        $file=$path."/Pages/$file";
        $composerDir=$path."/Pages";

        if($this->files->isDirectory($composerDir)){
            if($this->files->isFile($file))
                return $this->error($page.' File Already exists!');
            
            if(!$this->files->put($file, $contents))
                return $this->error('Something went wrong!');

            DB::table('entity_versions')->insert([ ['page' => $page, 'version' => '1.0'] ]);
            $this->info("$page generated!");
        }else{
            $this->files->makeDirectory($composerDir, 0777, true, true);

            if(!$this->files->put($file, $contents))
                return $this->error('Something went wrong!');

                
            DB::table('entity_versions')->insert([ ['page' => $page, 'version' => '1.0'] ]);
            $this->info("$page generated!");
        }

    }
}