<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScheduledEmail;
use App\Jobs\SendScheduledEmail;
use Carbon\Carbon;

class ProcessScheduledEmails extends Command
{
    protected $signature = 'emails:process';
    protected $description = 'Dispatch scheduled emails to the queue';

    public function handle()
    {
        $emails = ScheduledEmail::where('status', 'pending')
            ->where('send_at', '<=', Carbon::now())
            ->get();

        foreach ($emails as $email) {
            SendScheduledEmail::dispatch($email);
        }

        $this->info('Scheduled emails dispatched.');
    }
}
