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
            $table->id();
            $table->foreignId('edu_id')->constrained('education')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('thoughts');
            $table->string('thoughts_vid_path')->nullable();
            $table->string('thoughts_img_path')->nullable();
            $table->string('thoughts_link')->nullable();
            $table->timestamps();
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
