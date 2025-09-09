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
        Schema::create('financial_projections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('business_plan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('projection_name');
            $table->enum('scenario_type', ['optimistic', 'realistic', 'pessimistic', 'custom'])->default('realistic');
            $table->enum('projection_period', ['monthly', 'quarterly', 'yearly'])->default('yearly');
            $table->integer('projection_years')->default(3);
            $table->decimal('base_year_revenue', 15, 2)->default(0);
            $table->decimal('base_year_expenses', 15, 2)->default(0);
            $table->decimal('base_year_profit', 15, 2)->default(0);
            
            // Growth Assumptions
            $table->decimal('revenue_growth_rate', 8, 4)->default(0.1000); // 10%
            $table->decimal('expense_growth_rate', 8, 4)->default(0.0500); // 5%
            $table->decimal('inflation_rate', 8, 4)->default(0.0300); // 3%
            $table->decimal('market_growth_rate', 8, 4)->default(0.0500);
            $table->decimal('customer_acquisition_rate', 8, 4)->default(0.0200);
            $table->decimal('customer_retention_rate', 8, 4)->default(0.8000);
            $table->decimal('average_order_value_growth', 8, 4)->default(0.0200);
            
            // Scenario Variables
            $table->decimal('best_case_revenue_multiplier', 5, 2)->default(1.20);
            $table->decimal('worst_case_revenue_multiplier', 5, 2)->default(0.80);
            $table->json('seasonal_adjustments')->nullable();
            $table->decimal('market_penetration_rate', 8, 4)->default(0.0100);
            $table->json('pricing_changes')->nullable();
            
            // Projected Data (JSON arrays for multi-year projections)
            $table->json('projected_revenue')->nullable();
            $table->json('projected_cogs')->nullable();
            $table->json('projected_gross_profit')->nullable();
            $table->json('projected_operating_expenses')->nullable();
            $table->json('projected_net_profit')->nullable();
            $table->json('projected_cash_flow')->nullable();
            $table->json('projected_assets')->nullable();
            $table->json('projected_liabilities')->nullable();
            $table->json('projected_equity')->nullable();
            
            // Key Metrics
            $table->integer('break_even_month')->nullable();
            $table->integer('payback_period')->nullable();
            $table->decimal('roi_percentage', 8, 2)->nullable();
            $table->decimal('irr_percentage', 8, 2)->nullable();
            $table->decimal('npv_amount', 15, 2)->nullable();
            
            // Assumptions and Notes
            $table->json('key_assumptions')->nullable();
            $table->json('risk_factors')->nullable();
            $table->json('sensitivity_analysis')->nullable();
            $table->json('external_factors')->nullable();
            $table->string('currency', 3)->default('KES');
            $table->date('created_date')->default(now());
            $table->timestamp('last_updated')->default(now());
            $table->boolean('is_baseline')->default(false);
            $table->decimal('confidence_level', 5, 2)->default(75.00);
            
            $table->timestamps();

            // Index for better performance
            $table->index(['user_id', 'scenario_type']);
            $table->index(['business_plan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_projections');
    }
};
