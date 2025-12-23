<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Order;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        $pdf = Pdf::loadView('invoices.show', ['order' => $this->order]);

        return $this->subject('Your Invoice from BetaMore Limited')
                    ->view('emails.invoice')
                    ->attachData($pdf->output(), "BetaMoreLimited-{$this->order->order_number}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
    }
}
