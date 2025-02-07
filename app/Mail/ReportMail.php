<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $pdfPath;

    public function __construct($client, $pdfPath)
    {
        $this->client = $client;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->subject('Seu Relatório de Diagnóstico')
            ->view('emails.report')
            ->attach($this->pdfPath, [
                'as' => 'relatorio-diagnostico.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}

