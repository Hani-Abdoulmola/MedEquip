<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Order $order
    ) {
        // Load necessary relationships
        $this->order->load([
            'buyer.user',
            'supplier.user',
            'quotation.rfq',
            'items.product',
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "تأكيد الطلب رقم {$this->order->order_number}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.confirmation',
            with: [
                'order' => $this->order,
                'buyer' => $this->order->buyer,
                'supplier' => $this->order->supplier,
                'quotation' => $this->order->quotation,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        // Attach order PDF if it exists
        $orderPdf = $this->order->getFirstMedia('order_documents');
        if ($orderPdf) {
            $attachments[] = Attachment::fromPath($orderPdf->getPath())
                ->as("order_{$this->order->order_number}.pdf")
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}
