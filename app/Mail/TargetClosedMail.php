<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class TargetClosedMail extends Mailable
{
    public function build()
    {
        return $this->view('emails.target_closed')->subject('ISO setting is closed for this year');
    }
}
