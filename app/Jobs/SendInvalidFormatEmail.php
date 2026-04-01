<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Mail\FormatoInvalidoMail;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendInvalidFormatEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $nombreArchivo;

    public function __construct($email, $nombreArchivo)
    {
        $this->email = $email;
        $this->nombreArchivo = $nombreArchivo;
    }

    public function handle()
    {
        Mail::to($this->email)->send(new FormatoInvalidoMail($this->nombreArchivo));
    }
}
