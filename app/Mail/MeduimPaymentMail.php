<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MeduimPaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The payment instance.
     *
     * @var \App\Models\User
     */
    public $payment;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Payment  $payment
     * @return void
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }
   
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Thank You for Upgrading to Our Medium Plan!')
                    ->view('emails.meduim');
    }
}
