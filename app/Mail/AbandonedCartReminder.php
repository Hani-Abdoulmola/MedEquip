<?php

namespace App\Mail;

use App\Models\BuyerCart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbandonedCartReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public BuyerCart $cart,
        public string $reminderType = '24h'
    ) {
        // Load necessary relationships
        $this->cart->load([
            'buyer.user',
            'items.product.category',
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->reminderType) {
            '24h' => 'لديك منتجات في سلة طلبات العروض',
            '72h' => 'تذكير: لا تنسَ إكمال طلب عرض السعر',
            '7d' => 'آخر فرصة: سلة طلبات العروض الخاصة بك',
            default => 'تذكير بسلة طلبات العروض',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.cart.abandoned',
            with: [
                'cart' => $this->cart,
                'buyer' => $this->cart->buyer,
                'itemCount' => $this->cart->items->count(),
                'reminderType' => $this->reminderType,
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
        return [];
    }
}
