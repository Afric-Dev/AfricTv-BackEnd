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
        Schema::create('blogviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('post_id')->nullable();
            $table->uuid('user_id')->nullable();  
            $table->string('session_id')->nullable(); 
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });

        Schema::create('eduviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('edu_id')->nullable(); 
            $table->uuid('user_id')->nullable(); 
            $table->string('session_id')->nullable(); 
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('edu_id')->references('id')->on('education')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogviews');
        Schema::dropIfExists('eduviews');
    }
};
