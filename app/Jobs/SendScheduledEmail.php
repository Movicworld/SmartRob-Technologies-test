<?php

namespace App\Jobs;

use App\Models\ScheduledEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendScheduledEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;
    public $tries = 3; // Maximum number of retries

    /**
     * Create a new job instance.
     */
    public function __construct(ScheduledEmail $email)
    {
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            Mail::raw($this->email->body, function ($message) {
                $message->to($this->email->recipient_email)
                    ->subject($this->email->subject);
            });

            $this->email->update([
                'status' => 'sent',
                'error_message' => null
            ]);
        } catch (\Exception $e) {
            $retryCount = $this->email->retry_count + 1;

            if ($retryCount >= 3) {
                $this->email->update([
                    'status' => 'permanently_failed',
                    'error_message' => $e->getMessage()
                ]);
            } else {
                $this->email->update([
                    'status' => 'failed',
                    'retry_count' => $retryCount,
                    'error_message' => $e->getMessage()
                ]);

                self::dispatch($this->email)->delay(now()->addMinutes(2));
            }
        }
    }

}
