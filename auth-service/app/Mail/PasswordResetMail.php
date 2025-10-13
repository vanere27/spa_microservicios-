<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject('Recuperación de contraseña - Spa de Uñas')
                    ->view('emails.password_reset')
                    ->with([
                        'token' => $this->token,
                    ]);
    }
}
