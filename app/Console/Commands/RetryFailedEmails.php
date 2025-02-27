<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScheduledEmail;
use App\Jobs\SendScheduledEmail;

class RetryFailedEmails extends Command
{
    protected $signature = 'emails:retry-failed';
    protected $description = 'Retry sending failed emails that are not permanently failed';

    public function handle()
    {
        $failedEmails = ScheduledEmail::where('status', 'failed')->get();

        foreach ($failedEmails as $email) {
            if ($email->retry_count < 3) {
                SendScheduledEmail::dispatch($email)->delay(now()->addMinutes(2));
                $email->update([
                    'retry_count' => $email->retry_count + 1
                ]);
                $this->info("Retrying email ID {$email->id}");
            } else {
                $email->update(['status' => 'permanently_failed']);
                $this->warn("Email ID {$email->id} marked as permanently failed.");
            }
        }

        $this->info('Failed email retry process completed.');
    }
}
