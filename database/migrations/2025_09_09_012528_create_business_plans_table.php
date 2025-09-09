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
        Schema::create('business_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plan_name');
            $table->enum('plan_type', ['startup', 'expansion', 'loan_application', 'investor_pitch'])->default('startup');
            $table->string('version')->default('1.0');
            $table->enum('status', ['draft', 'review', 'final', 'approved'])->default('draft');
            
            // Executive Summary
            $table->text('executive_summary')->nullable();
            $table->text('business_concept')->nullable();
            $table->text('target_market')->nullable();
            $table->text('competitive_advantage')->nullable();
            $table->json('financial_highlights')->nullable();
            
            // Company Description
            $table->text('company_mission')->nullable();
            $table->text('company_vision')->nullable();
            $table->text('company_values')->nullable();
            $table->string('legal_structure')->nullable();
            $table->text('location_description')->nullable();
            $table->text('products_services_description')->nullable();
            
            // Market Analysis
            $table->text('market_size')->nullable();
            $table->text('market_trends')->nullable();
            $table->text('target_customer_profile')->nullable();
            $table->text('competitor_analysis')->nullable();
            $table->text('market_positioning')->nullable();
            
            // Marketing Strategy
            $table->text('marketing_objectives')->nullable();
            $table->text('pricing_strategy')->nullable();
            $table->text('promotional_strategy')->nullable();
            $table->text('sales_channels')->nullable();
            $table->text('customer_acquisition_strategy')->nullable();
            
            // Operations Plan
            $table->text('operational_workflow')->nullable();
            $table->text('staffing_plan')->nullable();
            $table->text('technology_requirements')->nullable();
            $table->text('supplier_relationships')->nullable();
            $table->text('quality_control')->nullable();
            
            // Management Team
            $table->text('management_structure')->nullable();
            $table->text('key_personnel')->nullable();
            $table->text('advisory_board')->nullable();
            $table->text('organizational_chart')->nullable();
            
            // Financial Projections
            $table->text('funding_requirements')->nullable();
            $table->json('revenue_projections')->nullable();
            $table->json('expense_projections')->nullable();
            $table->json('profit_projections')->nullable();
            $table->text('break_even_analysis')->nullable();
            $table->json('cash_flow_projections')->nullable();
            $table->text('financial_assumptions')->nullable();
            
            // Risk Analysis
            $table->text('market_risks')->nullable();
            $table->text('operational_risks')->nullable();
            $table->text('financial_risks')->nullable();
            $table->text('mitigation_strategies')->nullable();
            
            // Implementation Timeline
            $table->json('milestones')->nullable();
            $table->json('timeline')->nullable();
            $table->text('success_metrics')->nullable();
            
            // Appendices
            $table->json('supporting_documents')->nullable();
            $table->text('financial_statements')->nullable();
            $table->text('market_research_data')->nullable();
            $table->text('legal_documents')->nullable();
            
            // Metadata
            $table->string('created_for_purpose')->nullable();
            $table->string('target_audience')->nullable();
            $table->date('last_reviewed_date')->nullable();
            $table->date('next_review_date')->nullable();
            $table->json('auto_generated_sections')->nullable();
            $table->boolean('is_template')->default(false);
            $table->string('template_industry')->nullable();
            
            $table->timestamps();

            // Index for better performance
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_plans');
    }
};
