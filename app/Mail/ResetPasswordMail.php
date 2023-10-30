<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPasswordMail extends Mailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $email;
    public $name;
    public $tempPass;
  
    public function __construct($email, $name, $tempPass)
    {
        $this->email = $email;
        $this->name = $name;
        $this->tempPass = $tempPass;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('【模試システム】仮パスワードの再発行のお知らせ')
            ->text('emails.resetpassword');
    }
}