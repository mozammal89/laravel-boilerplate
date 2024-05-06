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
        Schema::table('transaction_requests', function (Blueprint $table) {
            $table->string('card_type')->nullable();
            $table->string('card_number_masked')->nullable();
            $table->string('card_expiry')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_requests', function (Blueprint $table) {
            //
        });
    }
};
