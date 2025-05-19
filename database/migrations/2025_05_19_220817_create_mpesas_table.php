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
        Schema::create('mpesas', function (Blueprint $table) {
            $table->id();
            $table->string('merchant_request_id')->index();
            $table->string('checkout_request_id')->index();
            $table->integer('result_code');
            $table->string('result_desc');
            $table->string('phone_number');
            $table->string('amount');
            $table->string('mpesa_receipt_number');
            $table->string('balance')->nullable();
            $table->string('transaction_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpesas');
    }
};
