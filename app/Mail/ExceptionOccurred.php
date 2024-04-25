<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ExceptionOccurred extends Mailable
{
    use Queueable, SerializesModels;

    public $exception;

    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }

    public function build()
    {
        return $this->view('emails.exception')
                    ->with([
                        'message' => $this->exception->getMessage(),
                        'file' => $this->exception->getFile(),
                        'line' => $this->exception->getLine()
                    ]);
    }
}
