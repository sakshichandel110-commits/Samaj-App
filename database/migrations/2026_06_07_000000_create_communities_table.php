<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //add already exist  condition
            if (!Schema::hasTable('communities')) { 
        Schema::create('communities', function (Blueprint $table) {
            $table->id();
            // use unsignedInteger to match users.id (non-big) as requested
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('community_logo')->nullable();
            $table->string('community_name');
            $table->string('community_short_name')->nullable();
            $table->text('community_description')->nullable();
            $table->string('admin_designation')->nullable();
            $table->string('code')->unique();
            $table->timestamps();
        });
    }
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('communities');
    }
}
