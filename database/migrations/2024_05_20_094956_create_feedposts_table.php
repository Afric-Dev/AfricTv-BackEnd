<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feedposts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('user_name');
            $table->string('unique_id');
            $table->string('user_email');
            $table->string('post_img_path');
            $table->string('post_vid_path');
            $table->string('post_pdf_path');
            $table->string('post_song_path');
            $table->string('category');
            $table->string('link')->nullable();
            $table->string('hashtags')->nullable();
            $table->string('post_body');
            $table->string('avatar_path');
            $table->string('user');
            $table->integer('post_views')->default(0); 
            $table->date('date')->nullable();
            $table->timestamps();

            // Define foreign key relationship with users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedposts');
    }
};
