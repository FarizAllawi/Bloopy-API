<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationUser extends Notification implements ShouldQueue
{
    use Queueable;
    
    private $type;
    private $user;
    private $code;
    private $platform;
    private $app;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($type, $user, $code, $platform, $app)
    {
        $this->type = $type;
        $this->user = $user;
        $this->code = $code;
        $this->platform = $platform;
        $this->app = $app;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if ($this->type === 'link') {
            return (new MailMessage)
                ->subject('Verify Account')
                ->markdown('emails.user.verification',[
                    'type' => $this->type,
                    'user' => $this->user,
                    'platform' => $this->platform,
                    'app' => $this->app
                ]);
        }

        return (new MailMessage)
                ->subject('Verify Account')
                ->markdown('emails.user.verification',[
                    'type' => $this->type,
                    'code' => $this->code,
                    'user' => $this->user,
                    'platform' => $this->platform,
                    'app' => $this->app
                ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
