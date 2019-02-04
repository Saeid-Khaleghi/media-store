<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->string('stored_name');
            $table->string('file_name');
            $table->string('extension',5);
            $table->string('caption')->nullable();
            $table->string('mime',30);
            $table->integer('size');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('mediumable_id')->nullable();
            $table->string('mediumable_type')->nullable();
            $table->tinyInteger('position')->nullable();
            $table->string('manner')->nullable();
            $table->integer('comments_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->string('description',500)->nullable();
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
        Schema::dropIfExists('media');
    }
}
