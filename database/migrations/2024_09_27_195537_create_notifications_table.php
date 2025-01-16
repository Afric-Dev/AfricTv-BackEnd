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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('receiver_id')->nullable(); 
            $table->string('post_id')->nullable(); 
            $table->string('edu_id')->nullable(); 
            $table->string('subscriber_unique_id')->nullable(); 
            $table->string('type'); 
            $table->string('title'); 
            $table->text('message'); 
            $table->boolean('is_read')->default(false); 
            $table->timestamp('read_at')->nullable(); 
            $table->timestamps();
            // Foreign key to user table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
