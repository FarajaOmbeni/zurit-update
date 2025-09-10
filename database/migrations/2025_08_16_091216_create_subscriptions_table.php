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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Subscription Details
            $table->string('package')->index(); // monthly, yearly
            $table->enum('status', ['pending', 'active', 'cancelled', 'expired', 'failed'])->default('pending')->index();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('KES');

            // Subscription Dates
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('cancelled_at')->nullable();

            // Pesapal Transaction Details
            $table->string('order_id')->unique();
            $table->string('order_tracking_id')->nullable()->unique();
            $table->string('order_merchant_reference')->nullable();
            $table->string('pesapal_transaction_id')->nullable();

            // Payment Status
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_method')->nullable(); // CARD, MOBILE, etc.
            $table->timestamp('payment_completed_at')->nullable();

            // Renewal Tracking
            $table->boolean('auto_renew')->default(true);
            $table->integer('renewal_attempts')->default(0);
            $table->timestamp('next_renewal_date')->nullable()->index();
            $table->timestamp('last_renewal_attempt_at')->nullable();
            $table->json('renewal_errors')->nullable(); // Store renewal error history

            // Parent/Child Relationship for Renewals
            $table->foreignId('parent_subscription_id')->nullable()->constrained('subscriptions')->onDelete('set null');
            $table->boolean('is_renewal')->default(false);

            // Additional Metadata
            $table->json('pesapal_response')->nullable(); // Store full Pesapal response
            $table->json('metadata')->nullable(); // Additional data
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['status', 'expires_at']);
            $table->index(['auto_renew', 'next_renewal_date']);
            $table->index(['payment_status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
