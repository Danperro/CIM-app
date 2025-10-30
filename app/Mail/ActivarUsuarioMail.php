<?php

namespace App\Mail;

use App\Models\usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class ActivarUsuarioMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public usuario $user, public string $url) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo usuario pendiente de activación',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.activar-usuario',             // <-- usa tu vista real
            with: ['user' => $this->user, 'url' => $this->url], // <-- pasa variables a la vista
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
