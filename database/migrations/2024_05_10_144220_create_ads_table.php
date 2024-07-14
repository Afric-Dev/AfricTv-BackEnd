<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('user_email'); 
            $table->string('img_vid_path');
            $table->string('title');
            $table->string('description');
            $table->string('link')->nullable(); 
            $table->datetime('start_date');
            $table->datetime('end_date'); 
            $table->string('status');
            $table->unsignedBigInteger('post_id'); 

            $table->timestamps();

            // Foreign key definition
            // $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ads');
    }
}
