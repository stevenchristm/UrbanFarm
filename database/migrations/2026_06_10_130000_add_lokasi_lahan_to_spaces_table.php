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
        // Add column only if it does not exist to avoid duplicate errors
        if (!Schema::hasColumn('spaces', 'lokasi_lahan')) {
            Schema::table('spaces', function (Blueprint $table) {
                $table->string('lokasi_lahan')->default('')->after('cahaya_lahan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('spaces', 'lokasi_lahan')) {
            Schema::table('spaces', function (Blueprint $table) {
                $table->dropColumn('lokasi_lahan');
            });
        }
    }
};
