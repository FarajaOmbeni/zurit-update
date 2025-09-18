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
        Schema::table('users', function (Blueprint $table) {
            $table->string('subscription_status', 20)
                ->default('inactive')
                ->after('remember_token'); // adjust placement as needed
            $table->timestamp('subscription_expires_at')
                ->nullable()
                ->after('subscription_status');
            $table->timestamp('last_payment_date')
                ->nullable()
                ->after('subscription_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_status',
                'subscription_expires_at',
                'last_payment_date',
            ]);
        });
    }
};
