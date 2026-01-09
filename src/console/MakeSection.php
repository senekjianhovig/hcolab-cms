<?php

namespace hcolab\cms\console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeSection extends Command
{

    protected $signature = 'make:section {section}';

    protected $files;
 
    protected $description = 'Generate a section';


    public function __construct(Filesystem $files)
    {
        $this->files=$files;
        parent::__construct();
    }

  
    public function handle()
    {
        $section=$this->argument('section');

        if ($section === '' || is_null($section) || empty($section)) {
            return $this->error('Section Name Invalid..!');
        }

        $settings= get_page_settings($section);
        $title = $settings['title'];

$contents=
'<?php
namespace App\Sections;
use hcolab\cms\repositories\Section;
        
class '.$section.' extends Section
{
        
    /**
    * Create a new '.$section.' composer.
    *
    * @return void
    */
    public function __construct()
    {
        $this->version = "1.0";
        $this->section = "'.$section.'";
        $this->title ="'.$title.'";
        $this->elements = collect([]);    
        $this->identifier ="";    
        $this->foreign_keys = [];
        $this->component = "";
        $this->locations = [];  
    }
        
 
    public function setElements(){
        return $this
        ->TextField("Label", "col-lg-12", false , "label");
    }
  
}';

        $file = "${section}.php";
        $path=app_path();
        $file=$path."/Sections/$file";
        $composerDir=$path."/Sections";

        if($this->files->isDirectory($composerDir)){
            if($this->files->isFile($file))
                return $this->error($page.' File Already exists!');
            
            if(!$this->files->put($file, $contents))
                return $this->error('Something went wrong!');

        }else{
            $this->files->makeDirectory($composerDir, 0777, true, true);

            if(!$this->files->put($file, $contents))
                return $this->error('Something went wrong!');

            $this->info("$section generated!");
        }

    }
}