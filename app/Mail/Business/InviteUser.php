<?php

namespace App\Mail\Business;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteUser extends Mailable
{
    use Queueable, SerializesModels;

    private $business;

    private $userRecipient;

    private $userSender;

    private $status;

    private $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($business, $userSender , $userRecipient, $status, $token)
    {
        $this->business = $business;
        $this->userSender = $userSender;
        $this->userRecipient = $userRecipient;
        $this->status = $status;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Business Invitation')
                    ->markdown('emails.business.businessInvitation', [
                        'business' => $this->business,
                        'sender' => $this->userSender,
                        'recipant' => $this->userRecipient,
                        'status' => $this->status,
                        'token' => $this->token
                    ]);
    }
}
