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
        Schema::table('assets', function (Blueprint $table) {
            $table->string('asset_type')->nullable()->after('type'); // For balance sheet categorization
            $table->decimal('current_value', 15, 2)->nullable()->after('value'); // Current market value
            $table->date('date_acquired')->nullable()->after('acquisition_date'); // Alias for acquisition_date
            $table->decimal('depreciation', 15, 2)->default(0)->after('current_value'); // Accumulated depreciation
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['asset_type', 'current_value', 'date_acquired', 'depreciation']);
        });
    }
};