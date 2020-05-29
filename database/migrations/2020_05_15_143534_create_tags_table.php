<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tag')->unique();
            $table->string('title');
            $table->string('subtitle');
            $table->string('page_image');//标签图片
            $table->string('meta_description');//标签介绍
            $table->string('layout')->default('blog.layouts.index');//博客终归要使用布局
            $table->boolean('reverse_direction'); //在文章列表按时间升序排列博客文章（默认是降序）
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
        Schema::dropIfExists('tags');
    }
}
