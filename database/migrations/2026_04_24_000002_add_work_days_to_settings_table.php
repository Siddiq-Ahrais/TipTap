<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->json('work_days')->nullable()->after('jam_mulai_pulang');
        });

        // Set default value for existing rows
        DB::table('settings')->whereNull('work_days')->update([
            'work_days' => json_encode([1, 2, 3, 4, 5]),
        ]);
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('work_days');
        });
    }
};
