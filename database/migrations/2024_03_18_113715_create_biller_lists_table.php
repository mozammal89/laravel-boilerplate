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
        Schema::create('biller_lists', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('group_id')->unsigned()->nullable();
            $table->foreign('group_id')->references('id')->on('biller_groups')->onDelete('cascade');
            $table->string('domain_code')->nullable();
            $table->string('wallet_alias')->nullable();
            $table->string('biller_name');
            $table->string('biller_pin')->nullable();
            $table->string('biller_category')->nullable();
            $table->string('transaction_type')->nullable();
            $table->boolean('use_group_credentials')->default(true);
            $table->string('credentials')->nullable();
            $table->string('availability')->nullable();
            $table->boolean('status')->default(false);
            $table->string('service_base_url')->nullable();
            $table->string('service_success_endpoint')->nullable();
            $table->json('service_credentials')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biller_lists');
    }
};
