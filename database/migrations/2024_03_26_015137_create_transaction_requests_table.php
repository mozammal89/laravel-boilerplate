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
        Schema::create('transaction_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('request_id')->unique();
            $table->string('service_identifier');
            $table->bigInteger('biller_id')->unsigned()->nullable();
            $table->foreign('biller_id')->references('id')->on('biller_lists')->nullOnDelete();
            $table->json('service_parameters');
            $table->enum('service_status', ['Pending', 'Processing', 'Delivered'])->default('Pending');
            $table->enum('payment_status', ['Pending', 'Processing', 'Cancelled', 'Failed', 'Successful', 'Declined'])->default('Pending');
            $table->string('payment_url')->nullable();
            $table->json('service_response_payload')->nullable();
            $table->json('payment_response_payload')->nullable();
            $table->timestamp('service_activation_time')->nullable();
            $table->timestamp('payment_completion_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_requests');
    }
};
