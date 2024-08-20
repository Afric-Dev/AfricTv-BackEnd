<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdInactiveNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $ad;

    public function __construct($ad)
    {
        $this->ad = $ad;
    }

    public function build()
    {
        return $this->subject('Your Ad has been Deactivated')
                    ->view('emails.ad_inactive');
    }
}
