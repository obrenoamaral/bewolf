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
    public $emailContent;

    public function __construct($client, $pdfPath, $emailContent)  // Passando emailContent como argumento
    {
        $this->client = $client;
        $this->pdfPath = $pdfPath;
        $this->emailContent = $emailContent;  // Atribuindo à variável da classe
    }

    public function build()
    {
        return $this->subject('Seu Relatório de Diagnóstico')
            ->view('emails.report')
            ->with([ 'emailContent' => $this->emailContent ])  // Passando a variável para a view
            ->attach($this->pdfPath, [
                'as' => 'relatorio-diagnostico.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}


