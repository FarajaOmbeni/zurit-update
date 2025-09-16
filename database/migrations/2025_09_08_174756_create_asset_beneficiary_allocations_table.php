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
        Schema::create('asset_beneficiary_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->foreignId('beneficiary_id')->constrained()->onDelete('cascade');
            $table->decimal('percentage', 5, 2); // 0-100 with 2 decimal places
            $table->text('conditions')->nullable();
            $table->foreignId('contingent_of')->nullable()->constrained('beneficiaries')->onDelete('cascade');
            $table->timestamps();

            // Ensure unique asset-beneficiary combination
            $table->unique(['asset_id', 'beneficiary_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_beneficiary_allocations');
    }
};
