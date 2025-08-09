<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('policy_number')->unique();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('category_id');
            $table->decimal('premium_amount', 15, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->json('custom_fields')->nullable();
            $table->string('tenant_id')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')
                ->on('clients')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('category_id')->references('id')
                ->on('categories')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->index(['tenant_id', 'end_date']);
            $table->index(['tenant_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurances');
    }
};
