<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('budget_planner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Foreign key to link to the user table
            $table->string('income_type');
            $table->decimal('expected_income', 10, 2);
            $table->decimal('actual_income', 10, 2)->nullable();
            $table->string('expense_type');
            $table->decimal('expected_expense', 10, 2);
            $table->decimal('actual_expense', 10, 2)->nullable();
            $table->date('date'); // Use DATE type to store only the month and year

            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }

    public function down()
    {
        Schema::dropIfExists('budget_planner');
    }
};
