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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('details_of_investment');
            $table->text('description')->nullable();
            $table->decimal('initial_amount', 15, 2);
            $table->decimal('current_amount', 15, 2);
            $table->date('start_date');
            $table->date('target_date')->nullable();
            $table->decimal('expected_return_rate', 8, 4)->nullable();
            $table->enum('frequency_of_return', ['monthly', 'yearly'])->nullable();
            $table->enum('status', ['active', 'sold', 'abandoned'])->default('active');
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
