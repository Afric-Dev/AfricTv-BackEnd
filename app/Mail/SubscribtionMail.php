<?php

namespace App\Mail;

use App\Models\Subscribtion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

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
     * The user instance.
     *
     * @var mixed
     */
    public $user;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Subscribtion  $subscribtion
     * @param  mixed  $user
     * @return void
     */
    public function __construct(Subscribtion $subscribtion, $user)
    {
        $this->subscribtion = $subscribtion;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('AfricTv New Subscriber Notification')
                    ->view('emails.Subscribtion');
    }
}
