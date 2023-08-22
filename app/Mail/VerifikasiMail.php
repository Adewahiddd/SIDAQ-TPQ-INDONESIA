<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifikasiMail extends Mailable
{
    // use Queueable, SerializesModels;

    // protected $user;
    // protected $isAccepted;

    // public function __construct($user, $isAccepted = true)
    // {
    //     $this->user = $user;
    //     $this->isAccepted = $isAccepted;
    // }

    // public function build()
    // {
    //     $subject = $this->isAccepted ? 'Registration Accepted' : 'Registration Rejected';
    //     $view = $this->isAccepted ? 'mail.verifikasi' : 'mail.reject';

    //     return $this->subject($subject)
    //                 ->view($view, ['user' => $this->user]);
    // }
}
