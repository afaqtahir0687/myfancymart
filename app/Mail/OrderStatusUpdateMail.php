<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $orderId;
    public $status;
    public $userName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($orderId, $status, $userName)
    {
        $this->orderId = $orderId;
        $this->status = $status;
        $this->userName = $userName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: translate('Order Status Update') . ' - ' . getWebConfig('company_name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email-templates.order-status-update',
        );
    }
}
