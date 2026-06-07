<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostReactionsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('post_reactions')) {
        Schema::create('post_reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('post_id');
            $table->boolean('liked')->default(false);
            $table->boolean('shared')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'post_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('matrimony_posts')->onDelete('cascade');
        });
    }
    }

    public function down()
    {
        Schema::dropIfExists('post_reactions');
    }
}
