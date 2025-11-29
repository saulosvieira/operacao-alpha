<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuoteAcceptedNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * A cotação que foi aceita.
     *
     * @var \App\Models\Quote
     */
    public $quote;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Quote  $quote
     * @return void
     */
    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "[Aceitação] Proposta #{$this->quote->quote_number} - " . ($this->quote->client->name ?? 'Cliente');
        
        return $this->subject($subject)
                    ->view('emails.quotes.accepted')
                    ->with([
                        'quote' => $this->quote,
                    ]);
    }
}
