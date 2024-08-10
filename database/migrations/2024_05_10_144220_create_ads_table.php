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
            $table->string('ads_id');
            $table->string('user_id'); 
            $table->string('user_email'); 
            $table->string('img_path')->nullable();
            $table->string('vid_path')->nullable();
            $table->string('title');
            $table->string('description');
            $table->string('link')->nullable(); 
            $table->datetime('start_date');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->nullable();
            $table->string('clicks');
            $table->string('ads_type');
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
