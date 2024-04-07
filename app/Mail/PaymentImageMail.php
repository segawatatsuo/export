<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentImageMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $content;
    public $title;

    public function __construct($content,$title )
    {
        $this->content = $content;
        $this->title = $title;
    }

    public function build()
    {
        return $this->from('export@ccmedico.com')
        ->subject($this->title)
        ->view('emails.paymentImageMail');
    }
}
