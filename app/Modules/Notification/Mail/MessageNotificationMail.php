<?php

namespace App\Modules\Notification\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MessageNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The data.
     *
     * @var string
     */
    public $data;

    /**
     * Create a new message instance.
     *
     * @param array $data
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->data['mailTitle'])
            ->view('notification::message_notification_mail', $this->data);
    }
}
