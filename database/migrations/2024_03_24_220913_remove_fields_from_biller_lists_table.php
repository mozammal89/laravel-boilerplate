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
        Schema::table('biller_lists', function (Blueprint $table) {
            $table->dropColumn('service_base_url');
            $table->dropColumn('service_success_endpoint');
            $table->dropColumn('service_credentials');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biller_lists', function (Blueprint $table) {
            //
        });
    }
};
