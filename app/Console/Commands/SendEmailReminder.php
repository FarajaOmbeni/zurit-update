<?php

namespace App\Console\Commands;

use App\Mail\ReminderMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEmailReminder extends Command
{
    protected $signature = 'app:send-email-reminder';

    protected $description = 'Reminding users to manage their portfolio';

    public function handle()
    {
        try {
            $users = User::whereNotNull('email')->get();

            $this->info("Starting to send emails to {$users->count()} users...");

            $successCount = 0;
            $failureCount = 0;

            foreach ($users as $user) {
                try {
                    Mail::to($user->email)->send(new ReminderMail($user));
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
