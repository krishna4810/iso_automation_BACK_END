<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class TargetOpenMail extends Mailable
{
    public function build()
    {
        return $this->view('emails.target_open')->subject('ISO setting is open for this year');
    }
}
