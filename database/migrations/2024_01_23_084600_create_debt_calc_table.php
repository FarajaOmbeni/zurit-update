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
        Schema::create('debt_calc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('category');
            $table->string('debt_name');
            $table->integer('current_balance');
            $table->integer('interest_rate');
            $table->integer('minimum_payment'); //This is a field to keep track of expenses and if the user wants to contrubute as they record the loan
            $table->integer('minimum_monthly_payment');
            $table->integer('extra_payment');
            $table->dateTime('start_period');
            $table->dateTime('end_period');
            $table->integer('number_of_months');
            //0=constant, 1=reducing
            $table->tinyInteger('payment_strategy')->default(0);
            //0=highestBalance, 1=highestRate
            $table->tinyInteger('debt_priority')->default(0);
            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debt_calc');
    }
};
