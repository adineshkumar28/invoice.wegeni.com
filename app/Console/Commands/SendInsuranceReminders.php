<?php

namespace App\Console\Commands;

use App\Models\Insurance;
use App\Notifications\InsuranceExpiryReminder;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendInsuranceReminders extends Command
{
    protected $signature = 'insurance:send-reminders';
    protected $description = 'Send insurance expiry reminder emails';

    public function handle()
    {
        $this->info('Starting insurance reminder process...');

        // Get insurances expiring in 30, 15, and 7 days
        $reminderDays = [30, 15, 7, 3, 1];
        
        foreach ($reminderDays as $days) {
            $targetDate = Carbon::now()->addDays($days)->format('Y-m-d');
            
            $insurances = Insurance::with('client')
                ->where('end_date', $targetDate)
                ->get();

            foreach ($insurances as $insurance) {
                if ($insurance->client && $insurance->client->email) {
                    $insurance->client->notify(new InsuranceExpiryReminder($insurances));
                    $this->info("Sent reminder for {$insurance->name} to {$insurance->client->email} ({$days} days)");
                }
            }
        }

        $this->info('Insurance reminders sent successfully!');
    }
}
