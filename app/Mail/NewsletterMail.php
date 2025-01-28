<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subject;
    public $content;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\User  $user
     * @param  string  $subject
     * @param  string  $content
     * @return void
     */
    public function __construct(User $user, $subject, $content)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.newsletter')
                    ->with([
                        'user' => $this->user,
                        'content' => $this->content,
                    ]);
    }
}
