<?php

namespace App\Mail;

use App\Models\Agent;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgentWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public Agent $agent;
    public Company $company;
    public ?string $recommendationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
        $this->company = $agent->company;
        $this->recommendationUrl = $agent->recommendation_url;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【Opinio ATS】エージェント登録のお知らせ',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.agent-welcome',
        );
    }
}
