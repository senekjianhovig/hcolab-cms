<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('disk', 255);
            $table->string('path', 255);
            $table->string('name', 255);
            $table->text('original_name');
            $table->string('mime_category', 255);
            $table->string('mime_type' , 255);
            $table->string('size' , 255);
            $table->string('extension', 20);
            $table->text('url');
            $table->boolean('external')->default(0);
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
        Schema::dropIfExists('files');
    }
}