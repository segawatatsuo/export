<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;
    public $content;
    public $title;
    public $items;

    public function __construct($content,$title,$items )
    {
        $this->content = $content;
        $this->title = $title;
        $this->items = $items;
    }

    public function build()
    {
        return $this->from('export@ccmedico.com')
        ->subject($this->title)
        ->view('emails.invoice');//テキストメールのビューを指定
    }
}
