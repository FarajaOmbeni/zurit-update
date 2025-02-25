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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->decimal('investment_type');
            $table->decimal('initial_investment', 15, 2)->nullable();
            $table->decimal('monthly_contribution')->nullable();
            $table->integer('total_investment')->nullable();
            $table->integer('mmf_name')->nullable();
            $table->string('details_of_investments')->nullable();
            $table->integer('number_of_months')->nullable();
            $table->integer('number_of_years')->nullable();
            $table->integer('number_of_days')->nullable();
            $table->decimal('rate_of_return', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
