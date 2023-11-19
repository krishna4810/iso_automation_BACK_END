<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class UpdateMail extends Mailable
{
    public function build()
    {
        return $this->view('emails.update')->subject('Update ISO Compliance System');
    }
}
