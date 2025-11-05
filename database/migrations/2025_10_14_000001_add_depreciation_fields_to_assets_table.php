<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'is_depreciable')) {
                $table->boolean('is_depreciable')->default(false)->after('depreciation');
            }
            if (!Schema::hasColumn('assets', 'useful_life_months')) {
                $table->unsignedInteger('useful_life_months')->nullable()->after('is_depreciable');
            }
            if (!Schema::hasColumn('assets', 'residual_value')) {
                $table->decimal('residual_value', 15, 2)->default(0)->after('useful_life_months');
            }
            if (!Schema::hasColumn('assets', 'depreciation_start')) {
                $table->date('depreciation_start')->nullable()->after('residual_value');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'depreciation_start')) {
                $table->dropColumn('depreciation_start');
            }
            if (Schema::hasColumn('assets', 'residual_value')) {
                $table->dropColumn('residual_value');
            }
            if (Schema::hasColumn('assets', 'useful_life_months')) {
                $table->dropColumn('useful_life_months');
            }
            if (Schema::hasColumn('assets', 'is_depreciable')) {
                $table->dropColumn('is_depreciable');
            }
        });
    }
};

