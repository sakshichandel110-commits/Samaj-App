<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatrimonyPostsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('matrimony_posts')) { 
        Schema::create('matrimony_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('name')->nullable();
            $table->integer('age')->nullable();
            $table->string('occupation')->nullable();
            $table->text('bio')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('verified')->default(false);
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('shares_count')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    }

    public function down()
    {
        Schema::dropIfExists('matrimony_posts');
    }
}
