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
        Schema::create('balance_sheet_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('as_of_date');
            
            // Current Assets
            $table->decimal('cash_and_equivalents', 15, 2)->default(0);
            $table->decimal('accounts_receivable', 15, 2)->default(0);
            $table->decimal('inventory', 15, 2)->default(0);
            $table->decimal('prepaid_expenses', 15, 2)->default(0);
            $table->decimal('other_current_assets', 15, 2)->default(0);
            
            // Non-Current Assets
            $table->decimal('property_plant_equipment', 15, 2)->default(0);
            $table->decimal('accumulated_depreciation', 15, 2)->default(0);
            $table->decimal('intangible_assets', 15, 2)->default(0);
            $table->decimal('investments', 15, 2)->default(0);
            $table->decimal('other_non_current_assets', 15, 2)->default(0);
            
            // Current Liabilities
            $table->decimal('accounts_payable', 15, 2)->default(0);
            $table->decimal('short_term_debt', 15, 2)->default(0);
            $table->decimal('accrued_liabilities', 15, 2)->default(0);
            $table->decimal('taxes_payable', 15, 2)->default(0);
            $table->decimal('other_current_liabilities', 15, 2)->default(0);
            
            // Non-Current Liabilities
            $table->decimal('long_term_debt', 15, 2)->default(0);
            $table->decimal('deferred_tax_liabilities', 15, 2)->default(0);
            $table->decimal('other_non_current_liabilities', 15, 2)->default(0);
            
            // Equity
            $table->decimal('share_capital', 15, 2)->default(0);
            $table->decimal('retained_earnings', 15, 2)->default(0);
            $table->decimal('other_equity', 15, 2)->default(0);
            
            $table->string('currency', 3)->default('KES');
            $table->boolean('is_automated')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index for better performance
            $table->index(['user_id', 'as_of_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_sheet_records');
    }
};
