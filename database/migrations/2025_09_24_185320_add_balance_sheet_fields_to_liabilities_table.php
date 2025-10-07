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
        Schema::table('liabilities', function (Blueprint $table) {
            $table->string('liability_type')->nullable()->after('category'); // For balance sheet categorization
            $table->decimal('current_balance', 15, 2)->nullable()->after('amount'); // Current outstanding balance
            $table->date('date_acquired')->nullable()->after('due_date'); // When the liability was incurred
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('liabilities', function (Blueprint $table) {
            $table->dropColumn(['liability_type', 'current_balance', 'date_acquired']);
        });
    }
};