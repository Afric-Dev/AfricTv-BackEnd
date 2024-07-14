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
       Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id'); // Primary key
            $table->unsignedBigInteger('user_id'); // Foreign key referencing users table
            $table->string('user_name');
            $table->string('unique_id');
            $table->string('user_email');
            $table->string('cover_image');
            $table->string('post_img_path');
            $table->string('post_vid_path');
            $table->string('post_pdf_path');
            $table->string('post_song_path');
            $table->string('category');
            $table->string('post_intro')->nullable();
            $table->string('post_title'); 
            $table->string('PostbodyHtml');
            $table->string('postbodyJson');
            $table->string('postBodytext');
            $table->string('link')->nullable();
            $table->string('hashtags')->nullable();
            $table->string('post_ending')->nullable();
            $table->integer('post_views')->default(0); // Assuming it's an integer type
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
        Schema::dropIfExists('post');
    }
};
