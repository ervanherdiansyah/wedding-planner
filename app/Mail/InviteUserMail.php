<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InviteUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $inviteLink;
    public $projectName;

    public function __construct($inviteLink, $projectName)
    {
        $this->inviteLink = $inviteLink;
        $this->projectName = $projectName;
    }

    public function build()
    {
        return $this->subject('Undangan ke Proyek ' . $this->projectName)
            ->view('emails.invite');
    }
}
