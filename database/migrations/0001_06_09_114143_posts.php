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
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('cover_image');
            $table->string('coverimageId');
            $table->string('post_img_path');
            $table->string('postimageId')->nullable();
            $table->string('post_vid_path');
            $table->string('postvideoId')->nullable();
            $table->string('post_pdf_path');
            $table->string('post_song_path');
            $table->string('category')->nullable();
            $table->string('post_intro')->nullable();
            $table->string('post_title'); 
            $table->text('PostbodyHtml');
            $table->text('postbodyJson')->nullable();
            $table->text('postBodytext');
            $table->string('link')->nullable();
            $table->string('hashtags')->nullable();
            $table->string('post_ending')->nullable();
            $table->integer('post_views')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('bookmark_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->enum('is_status', ['ACTIVE', 'INACTIVE', 'BANNED'])->default('ACTIVE');
            $table->date('date')->nullable();
            $table->timestamps();

            //Foreign key relationship with users table
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
