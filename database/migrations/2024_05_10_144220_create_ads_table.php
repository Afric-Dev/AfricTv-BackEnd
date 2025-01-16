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
            $table->uuid('id')->primary();
            $table->string('ads_id');
            $table->uuid('user_id');
            $table->string('img_path')->nullable();
            $table->string('imageId')->nullable();
            $table->string('vid_path')->nullable();
            $table->string('videoId')->nullable();
            $table->string('title');
            $table->string('description');
            $table->string('link')->nullable(); 
            $table->datetime('start_date');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->nullable();
            $table->string('clicks');
            $table->enum('ads_type', ['PIC', 'VID', 'LINK']);
            $table->enum('is_ads_type_sec', ['FEED', 'BANNER', 'SIDE']);
            $table->timestamps();

            // Foreign key definition
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
