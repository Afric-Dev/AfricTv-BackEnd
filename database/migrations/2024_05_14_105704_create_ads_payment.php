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
        Schema::create('ads_payments', function (Blueprint $table) {
            $table->id();
            $table->string('user_email');
            $table->string('user_name');
            $table->string('user_id');
            $table->string('amount');
            $table->string('ads_type');
            $table->string('duration'); //maybe for a 24hrs or a week or a month, per hr is about 0.069.... so the front end dev handling the payemtn int will give a input  where the user choose which plan they want
            $table->string('payment_type');
            $table->string('payment_status');
            $table->string('payment_method');
            $table->string('currency');
            $table->string('clicks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads_payments');
    }
};
