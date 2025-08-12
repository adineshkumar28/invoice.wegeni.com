<?php

namespace App\Console\Commands;

use App\Models\Insurance;
use App\Mail\InsuranceExpiryReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendInsuranceExpiryReminders extends Command
{
    protected $signature = 'insurance:send-expiry-reminders {--dry-run : Show what would be sent without actually sending}';
    protected $description = 'Send insurance expiry reminder emails at different intervals';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No emails will actually be sent');
            $this->newLine();
        }

        $this->info('🚀 Starting insurance expiry reminder process...');
        $this->newLine();

        $totalSent = 0;
        $reminders = [
            30 => 'general',
            15 => 'general', 
            7 => 'urgent',
            3 => 'urgent',
            1 => 'final'
        ];

        foreach ($reminders as $days => $type) {
            $sent = $this->sendRemindersForDays($days, $type, $isDryRun);
            $totalSent += $sent;
        }

        // Send daily reminders for expired policies
        $expiredSent = $this->sendExpiredReminders($isDryRun);
        $totalSent += $expiredSent;

        $this->newLine();
        if ($isDryRun) {
            $this->info("✅ DRY RUN COMPLETE: Would have sent {$totalSent} reminder emails");
        } else {
            $this->info("✅ PROCESS COMPLETE: Sent {$totalSent} reminder emails successfully");
        }

        Log::info("Insurance reminders process completed", [
            'total_sent' => $totalSent,
            'dry_run' => $isDryRun,
            'timestamp' => now()
        ]);
    }

    private function sendRemindersForDays(int $days, string $type, bool $isDryRun): int
    {
        $targetDate = Carbon::now()->addDays($days)->format('Y-m-d');
        
        $insurances = Insurance::with(['client'])
            ->where('end_date', $targetDate)
            ->get();

        $sent = 0;

        if ($insurances->count() > 0) {
            $this->info("📅 Processing {$days}-day reminders ({$type}) - {$insurances->count()} policies found");
            
            foreach ($insurances as $insurance) {
                if ($this->shouldSendReminder($insurance)) {
                    if ($isDryRun) {
                        $this->line("   📧 Would send {$type} reminder for: {$insurance->name} (Policy: {$insurance->policy_number}) to client");
                    } else {
                        try {
                            $this->sendReminderEmail($insurance, $type);
                            $this->line("   ✅ Sent {$type} reminder for: {$insurance->name} (Policy: {$insurance->policy_number})");
                            
                            // Log the reminder
                            Log::info("Insurance expiry reminder sent", [
                                'insurance_id' => $insurance->id,
                                'policy_number' => $insurance->policy_number,
                                'days_until_expiry' => $days,
                                'reminder_type' => $type,
                                'client_name' => $insurance->client_name ?? 'N/A'
                            ]);
                            
                        } catch (\Exception $e) {
                            $this->error("   ❌ Failed to send reminder for: {$insurance->name} - {$e->getMessage()}");
                            Log::error("Failed to send insurance expiry reminder", [
                                'insurance_id' => $insurance->id,
                                'policy_number' => $insurance->policy_number,
                                'error' => $e->getMessage(),
                                'days_until_expiry' => $days
                            ]);
                        }
                    }
                    $sent++;
                }
            }
        } else {
            $this->line("📅 No policies expiring in {$days} days");
        }

        return $sent;
    }

    private function sendExpiredReminders(bool $isDryRun): int
    {
        // Get policies that expired in the last 30 days (send daily reminders)
        $expiredInsurances = Insurance::with(['client'])
            ->where('end_date', '<', Carbon::now()->format('Y-m-d'))
            ->where('end_date', '>=', Carbon::now()->subDays(30)->format('Y-m-d'))
            ->get();

        $sent = 0;

        if ($expiredInsurances->count() > 0) {
            $this->info("🔴 Processing expired policy reminders - {$expiredInsurances->count()} expired policies found");
            
            foreach ($expiredInsurances as $insurance) {
                if ($this->shouldSendReminder($insurance)) {
                    if ($isDryRun) {
                        $this->line("   📧 Would send EXPIRED reminder for: {$insurance->name} (Policy: {$insurance->policy_number})");
                    } else {
                        try {
                            $this->sendReminderEmail($insurance, 'expired');
                            $this->line("   ✅ Sent EXPIRED reminder for: {$insurance->name} (Policy: {$insurance->policy_number})");
                            
                            Log::info("Insurance expired reminder sent", [
                                'insurance_id' => $insurance->id,
                                'policy_number' => $insurance->policy_number,
                                'days_since_expired' => abs($insurance->days_until_expiry),
                                'client_name' => $insurance->client_name ?? 'N/A'
                            ]);
                            
                        } catch (\Exception $e) {
                            $this->error("   ❌ Failed to send expired reminder for: {$insurance->name} - {$e->getMessage()}");
                            Log::error("Failed to send expired insurance reminder", [
                                'insurance_id' => $insurance->id,
                                'policy_number' => $insurance->policy_number,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                    $sent++;
                }
            }
        } else {
            $this->line("🔴 No recently expired policies found");
        }

        return $sent;
    }

    private function shouldSendReminder(Insurance $insurance): bool
    {
        // Check if insurance has a client with email
        if (!$insurance->client) {
            $this->warn("   ⚠️  Skipping {$insurance->name} - No client assigned");
            return false;
        }

        // Get client email through the proper relationship
        $clientEmail = null;
        if ($insurance->client && $insurance->client->user_id) {
            $user = \App\Models\User::withoutGlobalScope(\Stancl\Tenancy\Database\TenantScope::class)
                                   ->find($insurance->client->user_id);
            $clientEmail = $user->email ?? null;
        }

        if (!$clientEmail) {
            $this->warn("   ⚠️  Skipping {$insurance->name} - No client email found");
            return false;
        }

        return true;
    }

    private function sendReminderEmail(Insurance $insurance, string $type): void
    {
        // Get client email
        $clientEmail = null;
        if ($insurance->client && $insurance->client->user_id) {
            $user = \App\Models\User::withoutGlobalScope(\Stancl\Tenancy\Database\TenantScope::class)
                                   ->find($insurance->client->user_id);
            $clientEmail = $user->email;
        }

        if ($clientEmail) {
            Mail::to($clientEmail)->send(new InsuranceExpiryReminderMail($insurance, $type));
        } else {
            throw new \Exception('Client email not found');
        }
    }
}
