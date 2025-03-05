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
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type');
            $table->text('description')->nullable();
            $table->decimal('initial_amount', 15, 2);
            $table->decimal('current_amount', 15, 2)->default(0);
            $table->decimal('minimum_payment', 15, 2);
            $table->decimal('interest_rate', 8, 4);
            $table->date('start_date');
            $table->date('due_date')->nullable();
            $table->enum('status', ['active', 'paid_off'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
