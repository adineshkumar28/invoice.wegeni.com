<?php

namespace App\Notifications;

use App\Models\Insurance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InsuranceExpiryReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $insurance;

    public function __construct(Insurance $insurance)
    {
        $this->insurance = $insurance;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $daysUntilExpiry = $this->insurance->days_until_expiry;
        
        return (new MailMessage)
                    ->subject('Insurance Policy Expiry Reminder')
                    ->greeting('Hello ' . $notifiable->first_name . ' ' . $notifiable->last_name . ',')
                    ->line('This is a friendly reminder that your insurance policy is expiring soon.')
                    ->line('Policy Details:')
                    ->line('Insurance Name: ' . $this->insurance->name)
                    ->line('Policy Number: ' . $this->insurance->policy_number)
                    ->line('Expiry Date: ' . $this->insurance->end_date->format('d M Y'))
                    ->line('Days until expiry: ' . $daysUntilExpiry . ' days')
                    ->action('View Policy Details', url('/client/insurances/' . $this->insurance->id))
                    ->line('Please contact us if you need assistance with policy renewal.')
                    ->salutation('Best regards, ' . config('app.name'));
    }
}
