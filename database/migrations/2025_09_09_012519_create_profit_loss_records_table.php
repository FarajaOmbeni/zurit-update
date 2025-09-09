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
        Schema::create('profit_loss_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('revenue', 15, 2)->default(0);
            $table->decimal('cost_of_goods_sold', 15, 2)->default(0);
            $table->decimal('gross_profit', 15, 2)->default(0);
            $table->decimal('operating_expenses', 15, 2)->default(0);
            $table->decimal('other_income', 15, 2)->default(0);
            $table->decimal('other_expenses', 15, 2)->default(0);
            $table->decimal('ebitda', 15, 2)->default(0);
            $table->decimal('depreciation', 15, 2)->default(0);
            $table->decimal('amortization', 15, 2)->default(0);
            $table->decimal('interest_expense', 15, 2)->default(0);
            $table->decimal('interest_income', 15, 2)->default(0);
            $table->decimal('tax_expense', 15, 2)->default(0);
            $table->decimal('net_profit', 15, 2)->default(0);
            $table->string('currency', 3)->default('KES');
            $table->boolean('is_automated')->default(false); // true if auto-calculated from cashflow
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['user_id', 'period_start', 'period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_loss_records');
    }
};
