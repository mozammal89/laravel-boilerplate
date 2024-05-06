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
        Schema::create('otp_verification_manager', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('mobile', 11)->unique();
            $table->string('hash')->unique();
            $table->string('otp_code');
            $table->dateTime('sent_at');
            $table->dateTime('valid_till');
            $table->string('after_verification_step')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('forgot_password')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_verification_manager');
    }
};
