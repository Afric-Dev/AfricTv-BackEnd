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
        Schema::create('education', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('edu_id');
            $table->uuid('user_id');
           
            $table->string('title');
            $table->text('description');
            $table->string('links')->nullable();
            $table->string('edu_vid_path');
            $table->string('eduvideoId');
            $table->string('edu_views');
            $table->integer('vote_count')->default(0);
            $table->integer('favourites_count')->default(0);
            $table->integer('thoughts_count')->default(0);
            $table->enum('is_status', ['ACTIVE', 'INACTIVE', 'BANNED'])->default('ACTIVE');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education');
    }
};
