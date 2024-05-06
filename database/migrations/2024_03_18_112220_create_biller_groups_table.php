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
        Schema::create('biller_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon');
            $table->boolean('status')->default(false);
            $table->json('credentials')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biller_groups');
    }
};
