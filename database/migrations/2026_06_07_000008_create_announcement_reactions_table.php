<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementReactionsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('announcement_reactions')) {
        Schema::create('announcement_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('announcement_id');
            $table->boolean('liked')->default(false);
            $table->boolean('shared')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'announcement_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('announcement_id')->references('id')->on('announcements')->onDelete('cascade');
        });
    }
    }

    public function down()
    {
        Schema::dropIfExists('announcement_reactions');
    }
}
