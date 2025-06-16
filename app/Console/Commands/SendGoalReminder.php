<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Mail\GoalReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendGoalReminder extends Command
{
    protected $signature = 'app:send-goal-reminder';

    protected $description = 'Reminding users to add to their goals';

    public function handle()
    {
        try {
            $users = User::whereNotNull('email_verified_at')
                ->get();

            $this->info("Starting to send emails to {$users->count()} users...");

            $successCount = 0;
            $failureCount = 0;

            foreach ($users as $user) {
                try {
                    Mail::to($user->email)->send(new GoalReminderMail($user));
                    $successCount++;

                    usleep(100000); // 0.1 second delay

                    $this->info("Sent email to: {$user->name} ({$user->email})");
                } catch (\Exception $e) {
                    $failureCount++;
                    Log::error("Failed to send email to {$user->email}: " . $e->getMessage());
                    $this->error("Failed to send email to: {$user->name} ({$user->email})");
                }
            }

            $this->info("\nEmail sending completed!");
            $this->info("Successfully sent: {$successCount}");
            $this->error("Failed: {$failureCount}");
        } catch (\Exception $e) {
            Log::error("Email reminder command failed: " . $e->getMessage());
            $this->error("Command failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
