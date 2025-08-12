<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            // Add insurance-related columns
            $table->unsignedBigInteger('insurance_id')->nullable()->after('product_id');
            $table->string('insurance_name')->nullable()->after('product_name');
            $table->string('policy_number')->nullable()->after('insurance_name');
            $table->decimal('premium_amount', 15, 2)->nullable()->after('policy_number');
            $table->date('policy_start_date')->nullable()->after('premium_amount');
            $table->date('policy_end_date')->nullable()->after('policy_start_date');
            
            // Add foreign key for insurance
            $table->foreign('insurance_id')->references('id')
                ->on('insurances')
                ->onUpdate('cascade')
                ->onDelete('cascade');
                
            // Make product_id nullable since we're now using insurance_id
            $table->unsignedBigInteger('product_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign(['insurance_id']);
            $table->dropColumn([
                'insurance_id',
                'insurance_name', 
                'policy_number',
                'premium_amount',
                'policy_start_date',
                'policy_end_date'
            ]);
        });
    }
};
