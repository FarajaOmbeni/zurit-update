<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('month_end_closures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('opening_stock_value', 15, 2)->default(0);
            $table->decimal('purchases_total', 15, 2)->default(0);
            $table->decimal('closing_stock_value', 15, 2)->default(0);
            $table->decimal('calculated_cogs', 15, 2)->default(0);
            $table->boolean('depreciation_posted')->default(false);
            $table->timestamp('closed_at')->nullable();
            $table->boolean('locked')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'period_start', 'period_end']);
            $table->index(['user_id', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('month_end_closures');
    }
};

