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
        Schema::create('eduthoughts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('edu_id');
            $table->uuid('user_id');
            $table->string('parent_id')->nullable();
            $table->text('thoughts');
            $table->string('thoughts_vid_path')->nullable();
            $table->string('thoughts_img_path')->nullable();
            $table->string('thoughts_link')->nullable();
            $table->timestamps();
            //Forgien keys
            $table->foreign('parent_id')->references('id')->on('eduthoughts')->onDelete('cascade'); // Self-referencing foreign key
            $table->foreign('edu_id')->references('id')->on('education')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eduthoughts');
    }
};
