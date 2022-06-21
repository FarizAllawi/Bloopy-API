<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeNotification extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $code;
    private $platform;
    private $app;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $code, $platform, $app)
    {
        $this->user = $user;
        $this->code = $code;
        $this->platform = $platform;
        $this->app = $app;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Verify Account')->markdown('emails.user.verificationCode',[
            'code' => $this->code,
            'user' => $this->user,
            'platform' => $this->platform,
            'app' => $this->app
        ]);
    }
}
