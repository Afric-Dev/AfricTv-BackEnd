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
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('post_id'); 
            $table->uuid('user_id');
            $table->string('parent_id')->nullable();
            $table->text('comments');
            $table->string('comments_vid_path')->nullable();
            $table->string('comments_img_path')->nullable();
            $table->string('comments_link')->nullable();
            $table->timestamps();

            // Foreign key relationships
            $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade'); // Self-referencing foreign key
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
