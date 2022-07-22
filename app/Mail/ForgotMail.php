<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use File;

class ForgotMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data){
      $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){
      return $this->from('info@delimp.com','Alcoll')->subject("Alcoll:Forgot Password")->view('emails.forgot_password')->with('data', $this->data);
    }
}
