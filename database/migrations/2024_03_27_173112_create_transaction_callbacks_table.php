<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_callbacks', function (Blueprint $table) {
            $table->id();
            $table->string('request_id');
            $table->string('payment_reference');
            $table->string('transaction_reference')->nullable();
            $table->text('transaction_response')->nullable();
            $table->string('currency_code');
            $table->string('amount');
            $table->boolean('success')->default(false);
            $table->integer('status_code');
            $table->text('received_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_callbacks');
    }
};
