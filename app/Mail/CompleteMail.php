<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompleteMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;
        //コントローラーから送られた$contentをセット。View側では{{ $content['name'] }}
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('export@ccmedico.com')
        ->subject('C.C.Medico Thank you for your upload.')
        ->view('emails.completeMail')->with(['content' => $this->content]); 
    }
}
