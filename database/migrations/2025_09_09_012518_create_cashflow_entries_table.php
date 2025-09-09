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
        Schema::create('cashflow_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['income', 'expense']);
            $table->string('category');
            $table->string('subcategory')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->nullable(); // 'cash', 'bank', 'mpesa', 'credit', etc.
            $table->text('description')->nullable();
            $table->string('reference_number')->nullable();
            $table->date('entry_date');
            $table->boolean('is_recurring')->default(false);
            $table->string('business_unit')->nullable(); // for businesses with multiple units/departments
            $table->string('invoice_number')->nullable();
            $table->string('customer_supplier')->nullable();
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->timestamps();

            // Indexes for better performance
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'entry_date']);
            $table->index(['user_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashflow_entries');
    }
};
