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
        Schema::create('quote_item_taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quote_item_id')->nullable();
            $table->unsignedBigInteger('tax_id');
            $table->float('tax')->nullable();
            $table->timestamps();

            $table->foreign('quote_item_id')->references('id')->on('quote_items')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_item_taxes');
    }
};
