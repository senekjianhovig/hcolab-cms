<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemporaryFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if(Schema::hasTable('temporary_files')){
            return;
        }

        Schema::create('temporary_files', function (Blueprint $table) {
            $table->id();
            $table->string('disk', 255);
            $table->string('path', 255);
            $table->string('name', 255);
            $table->text('original_name', 255);
            $table->string('mime_category', 255);
            $table->string('mime_type' , 255);
            $table->string('size' , 255);
            $table->string('extension', 20);
            $table->text('url' , 255);
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temporary_files');
    }
}