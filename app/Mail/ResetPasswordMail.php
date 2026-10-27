<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

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
        $this->mailFromName = config('constants.MAIL_FROM_NAME');
        // $this->mailFrom = config('constants.MAIL_FROM_ADDRESS');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        return $this
            ->subject('【模試システム】仮パスワードの再発行のお知らせ')
            ->replyTo(config('constants.REPLY_TO'), $this->mailFromName)
            ->text('emails.resetpassword');
    }
}
