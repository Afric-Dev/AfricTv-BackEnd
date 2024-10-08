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
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('amount');
            $table->enum('ads_type', ['PIC', 'VID', 'LINK']);
            $table->string('duration'); 
            $table->enum('status', ['PAID', 'PENDING', 'FAILED']);
            $table->enum('method', ['PAYSTACK', 'PAYPAL'])->default('PAYSTACK');
            $table->string('currency');
            $table->string('clicks');
            $table->enum('taken', ['YES', 'NO']);
            $table->string('ads_id');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
