<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;

    /**
     * Create a new message instance.
     */
    public function __construct(string $name = 'Khách hàng')
    {
        $this->name = $name;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->subject('Test Mail – Giá Vàng Bạc')
            ->view('mail.test')
            ->with([
                'name' => $this->name,
            ]);
    }
}
