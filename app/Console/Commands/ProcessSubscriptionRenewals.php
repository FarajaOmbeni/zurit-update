<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Subscription;
use App\Services\PesapalService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessSubscriptionRenewals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:renew {--dry-run : Run without making actual changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process automatic subscription renewals for users';

    private $pesapalService;

    public function __construct(PesapalService $pesapalService)
    {
        parent::__construct();
        $this->pesapalService = $pesapalService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('Running in DRY-RUN mode - no actual changes will be made');
        }

        // Find subscriptions that need renewal
        $subscriptionsForRenewal = Subscription::forRenewal()->get();
        $this->info("Found {$subscriptionsForRenewal->count()} subscriptions ready for renewal");

        $renewedCount = 0;
        $failedCount = 0;

        foreach ($subscriptionsForRenewal as $subscription) {
            try {
                $this->processSubscriptionRenewal($subscription, $isDryRun);
                $renewedCount++;
            } catch (\Exception $e) {
                $failedCount++;
                $this->error("Failed to renew subscription {$subscription->id} for user {$subscription->user->email}: {$e->getMessage()}");

                Log::error('Subscription renewal failed', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'email' => $subscription->user->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Find and expire overdue subscriptions
        $expiredSubscriptions = Subscription::expired()->get();
        $this->info("Found {$expiredSubscriptions->count()} expired subscriptions");

        foreach ($expiredSubscriptions as $subscription) {
            if (!$isDryRun) {
                $subscription->expire();
                Log::info('Subscription expired', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'email' => $subscription->user->email
                ]);
            }
            $this->info("Expired subscription {$subscription->id} for {$subscription->user->email}");
        }

        // Check for failed renewal attempts
        $failedRenewals = Subscription::failedRenewals(3)->get();
        $this->info("Found {$failedRenewals->count()} subscriptions with failed renewal attempts");

        foreach ($failedRenewals as $subscription) {
            if (!$isDryRun) {
                $subscription->update(['auto_renew' => false]);
                Log::warning('Subscription auto-renewal disabled after multiple failures', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'renewal_attempts' => $subscription->renewal_attempts
                ]);
            }
            $this->warn("Disabled auto-renewal for subscription {$subscription->id} after {$subscription->renewal_attempts} failed attempts");
        }

        $this->info("Renewal process completed:");
        $this->info("- Successfully processed: {$renewedCount}");
        $this->info("- Failed: {$failedCount}");
        $this->info("- Expired: {$expiredSubscriptions->count()}");
        $this->info("- Failed renewals disabled: {$failedRenewals->count()}");

        return Command::SUCCESS;
    }

    /**
     * Process renewal for a single subscription
     */
    private function processSubscriptionRenewal(Subscription $subscription, bool $isDryRun)
    {
        $user = $subscription->user;

        $this->info("Processing renewal for subscription {$subscription->id} - {$user->email} (package: {$subscription->package})");

        if ($isDryRun) {
            $this->info("DRY-RUN: Would create renewal order for subscription {$subscription->id}");
            return;
        }

        // Create renewal order using the PesapalService
        $orderResponse = $this->pesapalService->createRenewalOrder($subscription);

        if (!$orderResponse || !isset($orderResponse['order_tracking_id'])) {
            throw new \Exception('Failed to create renewal order');
        }

        // Log the renewal attempt
        Log::info('Subscription renewal initiated', [
            'subscription_id' => $subscription->id,
            'user_id' => $user->id,
            'email' => $user->email,
            'package' => $subscription->package,
            'order_tracking_id' => $orderResponse['order_tracking_id']
        ]);

        $this->info("Renewal order created for subscription {$subscription->id} (Order: {$orderResponse['order_tracking_id']})");
    }
}
