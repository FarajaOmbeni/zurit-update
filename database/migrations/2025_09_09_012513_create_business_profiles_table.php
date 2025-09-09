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
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('business_name');
            $table->string('business_type')->nullable(); // 'manufacturing', 'retail', 'service', 'agriculture', etc.
            $table->string('industry_sector')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('tax_pin')->nullable();
            $table->string('vat_number')->nullable();
            $table->text('business_address')->nullable();
            $table->string('business_phone')->nullable();
            $table->string('business_email')->nullable();
            $table->string('website')->nullable();
            $table->integer('employees_count')->nullable();
            $table->decimal('annual_revenue', 15, 2)->nullable();
            $table->integer('business_age_years')->nullable();
            $table->string('primary_market')->nullable(); // 'local', 'regional', 'national', 'international'
            $table->text('target_customers')->nullable();
            $table->text('main_products_services')->nullable();
            $table->text('business_description')->nullable();
            $table->string('operational_status')->default('active'); // 'active', 'seasonal', 'dormant'
            $table->date('fiscal_year_start')->nullable();
            $table->date('fiscal_year_end')->nullable();
            $table->string('currency', 3)->default('KES'); // 'KES', 'USD', etc.
            $table->string('timezone')->default('Africa/Nairobi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_profiles');
    }
};
