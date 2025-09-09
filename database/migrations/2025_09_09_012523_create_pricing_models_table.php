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
        Schema::create('pricing_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('product_service_name');
            $table->enum('product_type', ['product', 'service']);
            $table->string('industry_template')->nullable(); // 'manufacturing', 'retail', 'service', 'agriculture', etc.
            $table->decimal('raw_material_cost', 15, 2)->default(0);
            $table->decimal('direct_labor_cost', 15, 2)->default(0);
            $table->decimal('variable_overhead_cost', 15, 2)->default(0);
            $table->decimal('fixed_overhead_cost', 15, 2)->default(0);
            $table->decimal('total_cost_per_unit', 15, 2)->default(0);
            $table->decimal('desired_profit_margin', 5, 2)->default(0); // percentage
            $table->decimal('markup_percentage', 5, 2)->default(0);
            $table->decimal('suggested_selling_price', 15, 2)->default(0);
            $table->decimal('break_even_quantity', 15, 2)->default(0);
            $table->decimal('break_even_revenue', 15, 2)->default(0);
            $table->decimal('competitor_price_low', 15, 2)->nullable();
            $table->decimal('competitor_price_high', 15, 2)->nullable();
            $table->enum('market_positioning', ['premium', 'competitive', 'budget'])->nullable();
            $table->integer('units_per_period')->default(0);
            $table->enum('period_type', ['daily', 'weekly', 'monthly', 'yearly'])->default('monthly');
            $table->decimal('seasonal_adjustment', 5, 2)->default(0);
            $table->string('currency', 3)->default('KES');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index for better performance
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_models');
    }
};
