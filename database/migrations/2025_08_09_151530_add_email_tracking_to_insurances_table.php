<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('insurances', function (Blueprint $table) {
            
             $table->json('reminder_emails_sent')->nullable()->after('custom_fields');
            $table->timestamp('last_reminder_sent_at')->nullable()->after('reminder_emails_sent');
            $table->integer('total_reminders_sent')->default(0)->after('last_reminder_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insurances', function (Blueprint $table) {
              $table->dropColumn(['reminder_emails_sent', 'last_reminder_sent_at', 'total_reminders_sent']);
        });
    }
};
