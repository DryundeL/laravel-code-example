<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected string $email;
    protected Mailable $mail;

    public function __construct(string $email, Mailable $mail)
    {
        $this->email = $email;
        $this->mail = $mail;
    }

    public function handle(): void
    {
        Log::channel('notifications')->info('Sending mail to', ['email' => $this->email]);

        try {
            Mail::to($this->email)->send($this->mail);
        } catch (\Throwable $e) {
            Log::channel('notifications')->error('Mail sending failed', [
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);

            $this->fail($e);
        }
    }
}
