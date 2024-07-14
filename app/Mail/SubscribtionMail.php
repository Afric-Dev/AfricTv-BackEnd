<?php

namespace App\Mail;

use App\Models\Subscribtion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SubscribtionMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The subscription instance.
     *
     * @var \App\Models\Subscribtion
     */
    public $subscribtion;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Subscribtion  $subscribtion
     * @return void
     */
    public function __construct(Subscribtion $subscribtion)
    {
        $this->subscribtion = $subscribtion;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('AfricTv New Subscriber Notification')
                    ->view('emails.subscribtion');
    }
}
