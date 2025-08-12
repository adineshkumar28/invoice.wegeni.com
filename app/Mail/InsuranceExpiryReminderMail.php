<?php

namespace App\Mail;

use App\Models\Insurance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InsuranceExpiryReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $insurance;
    public $reminderType;
    public $daysUntilExpiry;

    public function __construct(Insurance $insurance, $reminderType = 'general')
    {
        $this->insurance = $insurance;
        $this->reminderType = $reminderType;
        $this->daysUntilExpiry = $insurance->days_until_expiry;
    }

    public function envelope(): Envelope
    {
        $subject = match($this->reminderType) {
            'urgent' => 'URGENT: Insurance Policy Expiring Soon - ' . $this->insurance->name,
            'final' => 'FINAL NOTICE: Insurance Policy Expires Tomorrow - ' . $this->insurance->name,
            'expired' => 'EXPIRED: Insurance Policy Has Expired - ' . $this->insurance->name,
            default => 'Insurance Policy Expiry Reminder - ' . $this->insurance->name
        };

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.insurance-expiry-reminder',
            with: [
                'insurance' => $this->insurance,
                'reminderType' => $this->reminderType,
                'daysUntilExpiry' => $this->daysUntilExpiry,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
