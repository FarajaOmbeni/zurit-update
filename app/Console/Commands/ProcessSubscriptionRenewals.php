<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
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

        // Find users whose subscriptions expire in the next 3 days
        $expiringUsers = User::where('subscription_status', 'active')
            ->where('subscription_expires_at', '>', now())
            ->where('subscription_expires_at', '<=', now()->addDays(3))
            ->whereNotNull('subscription_package')
            ->get();

        $this->info("Found {$expiringUsers->count()} users with expiring subscriptions");

        $renewedCount = 0;
        $failedCount = 0;

        foreach ($expiringUsers as $user) {
            try {
                $this->processUserRenewal($user, $isDryRun);
                $renewedCount++;
            } catch (\Exception $e) {
                $failedCount++;
                $this->error("Failed to renew subscription for user {$user->email}: {$e->getMessage()}");
                
                Log::error('Subscription renewal failed', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Find and expire overdue subscriptions
        $overdueUsers = User::where('subscription_status', 'active')
            ->where('subscription_expires_at', '<', now())
            ->get();

        $this->info("Found {$overdueUsers->count()} users with overdue subscriptions");

        foreach ($overdueUsers as $user) {
            if (!$isDryRun) {
                $user->update(['subscription_status' => 'expired']);
                Log::info('Subscription expired', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }
            $this->info("Expired subscription for {$user->email}");
        }

        $this->info("Renewal process completed:");
        $this->info("- Successfully processed: {$renewedCount}");
        $this->info("- Failed: {$failedCount}");
        $this->info("- Expired: {$overdueUsers->count()}");

        return Command::SUCCESS;
    }

    /**
     * Process renewal for a single user
     */
    private function processUserRenewal(User $user, bool $isDryRun)
    {
        $package = $user->subscription_package;
        
        if (!$package) {
            throw new \Exception('User has no subscription package defined');
        }

        $this->info("Processing renewal for {$user->email} (package: {$package})");

        if ($isDryRun) {
            $this->info("DRY-RUN: Would create renewal order for {$user->email}");
            return;
        }

        // Create renewal order
        $orderResponse = $this->pesapalService->createSubscriptionOrder($user, $package, 'RENEWAL_' . strtoupper(uniqid()));

        if (!$orderResponse || !isset($orderResponse['order_tracking_id'])) {
            throw new \Exception('Failed to create renewal order');
        }

        // Log the renewal attempt
        Log::info('Subscription renewal initiated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'package' => $package,
            'order_tracking_id' => $orderResponse['order_tracking_id']
        ]);

        // Note: In a real implementation, you might want to:
        // 1. Store the order tracking ID for later verification
        // 2. Send an email notification to the user
        // 3. Set up a follow-up check to verify payment completion
        
        $this->info("Renewal order created for {$user->email} (Order: {$orderResponse['order_tracking_id']})");
    }
}
