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
            $table->id(); // Primary key
            $table->string('post_id'); // Foreign key column
            $table->string('post_email');
            $table->string('user_id');
            $table->string('user_email');
            $table->string('user_name');
            $table->string('unique_id');
            $table->text('comments');
            $table->string('comments_vid_path')->nullable();
            $table->string('comments_img_path')->nullable();
            $table->string('comments_link')->nullable();
            $table->date('date');
            $table->timestamps();

            // Foreign key definition
            // $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
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

