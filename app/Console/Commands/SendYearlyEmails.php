<?php

namespace App\Console\Commands;

use App\Mail\TargetClosedMail;
use App\Mail\TargetOpenMail;
use App\Mail\UpdateMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Carbon\Carbon;

class SendYearlyEmails extends Command
{
    protected $signature = 'send:yearly-emails';
    protected $description = 'Send yearly emails to users';

    public function handle()
    {
        $this->sendTargetOpenEmail();
        $this->sendUpdateEmail();
        $this->sendTargetClosedEmail();
    }

    private function sendTargetOpenEmail()
    {
        $users = User::all();
        foreach ($users as $user) {
            Mail::to($user->email)->send(new TargetOpenMail());
        }
    }

    private function sendUpdateEmail()
    {
        $users = User::all();
        foreach ($users as $user) {
            Mail::to($user->email)->send(new UpdateMail());
        }
    }

    private function sendTargetClosedEmail()
    {
        $users = User::all();
        foreach ($users as $user) {
            Mail::to($user->email)->send(new TargetClosedMail());
        }
    }
}
