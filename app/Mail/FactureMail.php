<?php

namespace App\Mail;

use App\Models\Facture;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FactureMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Facture $facture,
        public string  $downloadUrl
    ) {}

    public function envelope(): Envelope
    {
        $entreprise = $this->facture->entreprise->nom;

        return new Envelope(
            subject: "Votre facture {$this->facture->numero} — {$entreprise}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.facture',
        );
    }

    public function attachments(): array
    {
        $facture = $this->facture->load('commande.lignes.produit', 'entreprise');

        $pdf = Pdf::loadView('factures.pdf', compact('facture'));
        $pdf->setPaper('A4', 'portrait');

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                "facture-{$this->facture->numero}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}
