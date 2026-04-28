<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $product;
    public $type;
    public $messageStr;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($product, $type)
    {
        $this->product = $product;
        $this->type = $type;

        if ($this->type == 'new') {
            $this->messageStr = translate('A new product has been added to our catalog that you might love!');
        } else {
            $this->messageStr = translate('A discount has been applied to a product you might be interested in!');
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->type == 'new' ? translate('New Product Added!') : translate('Discount Applied!');
        return new Envelope(
            subject: $subject . ' - ' . getWebConfig('company_name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email-templates.product-notification',
        );
    }
}
