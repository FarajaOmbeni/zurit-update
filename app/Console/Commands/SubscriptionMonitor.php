<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:monitor {--detailed : Show detailed information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor subscription status and health metrics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $detailed = $this->option('detailed');

        $this->info('🔍 Subscription Health Monitor');
        $this->info('================================');

        // Overall Stats
        $this->displayOverallStats();

        // Subscription Status Breakdown
        $this->displayStatusBreakdown();

        // Expiring Subscriptions
        $this->displayExpiringSubscriptions();

        // Failed Renewals
        $this->displayFailedRenewals();

        // Revenue Analytics
        $this->displayRevenueAnalytics();

        if ($detailed) {
            $this->displayDetailedAnalytics();
        }

        return Command::SUCCESS;
    }

    private function displayOverallStats()
    {
        $totalUsers = User::count();
        $totalSubscriptions = Subscription::count();
        $activeSubscriptions = Subscription::active()->count();
        $monthlyRevenue = Subscription::where('status', 'active')
            ->where('package', 'monthly')
            ->sum('amount');
        $yearlyRevenue = Subscription::where('status', 'active')
            ->where('package', 'yearly')
            ->sum('amount');

        $this->info("\n📊 Overall Statistics:");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Users', number_format($totalUsers)],
                ['Total Subscriptions', number_format($totalSubscriptions)],
                ['Active Subscriptions', number_format($activeSubscriptions)],
                ['Monthly Recurring Revenue', 'KES ' . number_format($monthlyRevenue)],
                ['Yearly Revenue', 'KES ' . number_format($yearlyRevenue)],
                ['Total MRR Equivalent', 'KES ' . number_format($monthlyRevenue + ($yearlyRevenue / 12))],
            ]
        );
    }

    private function displayStatusBreakdown()
    {
        $statuses = Subscription::selectRaw('status, COUNT(*) as count, SUM(amount) as revenue')
            ->groupBy('status')
            ->get();

        $this->info("\n📈 Subscription Status Breakdown:");
        $headers = ['Status', 'Count', 'Revenue (KES)'];
        $rows = [];

        foreach ($statuses as $status) {
            $rows[] = [
                ucfirst($status->status),
                number_format($status->count),
                'KES ' . number_format($status->revenue, 2)
            ];
        }

        $this->table($headers, $rows);
    }

    private function displayExpiringSubscriptions()
    {
        $expiring3Days = Subscription::expiring(3)->count();
        $expiring7Days = Subscription::expiring(7)->count();
        $expiring30Days = Subscription::expiring(30)->count();

        $this->info("\n⏰ Expiring Subscriptions:");
        $this->table(
            ['Time Frame', 'Count', 'Action Required'],
            [
                ['Next 3 days', $expiring3Days, $expiring3Days > 0 ? '🔄 Auto-renewal should trigger' : '✅ None'],
                ['Next 7 days', $expiring7Days, $expiring7Days > 0 ? '📧 Consider reminder emails' : '✅ None'],
                ['Next 30 days', $expiring30Days, $expiring30Days > 0 ? '📋 Monitor closely' : '✅ None'],
            ]
        );

        if ($expiring3Days > 0) {
            $this->warn("⚠️  {$expiring3Days} subscriptions expiring in 3 days - ensure renewal process is active!");
        }
    }

    private function displayFailedRenewals()
    {
        $failedRenewals = Subscription::failedRenewals(1)->get();
        $criticalFailures = Subscription::failedRenewals(3)->get();

        $this->info("\n❌ Failed Renewal Analysis:");
        $this->table(
            ['Category', 'Count', 'Status'],
            [
                ['Subscriptions with failed attempts', $failedRenewals->count(), $failedRenewals->count() > 0 ? '⚠️ Needs attention' : '✅ Good'],
                ['Critical failures (3+ attempts)', $criticalFailures->count(), $criticalFailures->count() > 0 ? '🚨 Urgent action required' : '✅ Good'],
            ]
        );

        if ($criticalFailures->count() > 0) {
            $this->error("🚨 {$criticalFailures->count()} subscriptions have failed renewal 3+ times!");

            foreach ($criticalFailures->take(5) as $subscription) {
                $this->line("   - Subscription #{$subscription->id} (User: {$subscription->user->email}) - {$subscription->renewal_attempts} attempts");
            }

            if ($criticalFailures->count() > 5) {
                $this->line("   ... and " . ($criticalFailures->count() - 5) . " more");
            }
        }
    }

    private function displayRevenueAnalytics()
    {
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        $thisMonthRevenue = Subscription::where('payment_completed_at', '>=', $thisMonth)
            ->where('payment_status', 'completed')
            ->sum('amount');

        $lastMonthRevenue = Subscription::where('payment_completed_at', '>=', $lastMonth)
            ->where('payment_completed_at', '<', $thisMonth)
            ->where('payment_status', 'completed')
            ->sum('amount');

        $growth = $lastMonthRevenue > 0 ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

        $this->info("\n💰 Revenue Analytics:");
        $this->table(
            ['Period', 'Revenue (KES)', 'Growth'],
            [
                ['This Month', 'KES ' . number_format($thisMonthRevenue, 2), ''],
                ['Last Month', 'KES ' . number_format($lastMonthRevenue, 2), ''],
                ['Growth Rate', '', number_format($growth, 1) . '%' . ($growth > 0 ? ' 📈' : ($growth < 0 ? ' 📉' : ' ➡️'))],
            ]
        );
    }

    private function displayDetailedAnalytics()
    {
        $this->info("\n🔍 Detailed Analytics:");

        // Package Distribution
        $packages = Subscription::selectRaw('package, COUNT(*) as count, AVG(amount) as avg_amount')
            ->where('status', 'active')
            ->groupBy('package')
            ->get();

        $this->info("\n📦 Package Distribution (Active):");
        foreach ($packages as $package) {
            $this->line("   {$package->package}: {$package->count} subscribers (Avg: KES " . number_format($package->avg_amount, 2) . ")");
        }

        // Renewal Success Rate
        $totalRenewals = Subscription::where('is_renewal', true)->count();
        $successfulRenewals = Subscription::where('is_renewal', true)
            ->where('status', 'active')
            ->count();

        $successRate = $totalRenewals > 0 ? ($successfulRenewals / $totalRenewals) * 100 : 0;

        $this->info("\n🔄 Renewal Success Rate:");
        $this->line("   Total Renewals: {$totalRenewals}");
        $this->line("   Successful: {$successfulRenewals}");
        $this->line("   Success Rate: " . number_format($successRate, 1) . "%");

        // Recent Activity
        $recentSubscriptions = Subscription::where('created_at', '>=', now()->subDays(7))->count();
        $recentPayments = Subscription::where('payment_completed_at', '>=', now()->subDays(7))->count();

        $this->info("\n📅 Recent Activity (Last 7 Days):");
        $this->line("   New Subscriptions: {$recentSubscriptions}");
        $this->line("   Completed Payments: {$recentPayments}");

        // Health Score
        $healthScore = $this->calculateHealthScore();
        $this->info("\n🏥 Subscription Health Score: {$healthScore}/100");

        if ($healthScore >= 80) {
            $this->info("   Status: 🟢 Excellent");
        } elseif ($healthScore >= 60) {
            $this->warn("   Status: 🟡 Good");
        } else {
            $this->error("   Status: 🔴 Needs Attention");
        }
    }

    private function calculateHealthScore(): int
    {
        $score = 100;

        // Deduct points for failed renewals
        $failedRenewals = Subscription::failedRenewals(3)->count();
        $totalActive = Subscription::active()->count();

        if ($totalActive > 0) {
            $failureRate = ($failedRenewals / $totalActive) * 100;
            $score -= min(30, $failureRate * 3); // Max 30 points deduction
        }

        // Deduct points for expiring subscriptions without auto-renewal
        $expiringWithoutRenewal = Subscription::expiring(3)
            ->where('auto_renew', false)
            ->count();

        if ($totalActive > 0) {
            $noRenewalRate = ($expiringWithoutRenewal / $totalActive) * 100;
            $score -= min(20, $noRenewalRate * 2); // Max 20 points deduction
        }

        // Bonus points for recent growth
        $thisMonth = Subscription::where('created_at', '>=', now()->startOfMonth())->count();
        $lastMonth = Subscription::where('created_at', '>=', now()->subMonth()->startOfMonth())
            ->where('created_at', '<', now()->startOfMonth())
            ->count();

        if ($lastMonth > 0 && $thisMonth > $lastMonth) {
            $score += min(10, (($thisMonth - $lastMonth) / $lastMonth) * 10);
        }

        return max(0, min(100, (int) $score));
    }
}
