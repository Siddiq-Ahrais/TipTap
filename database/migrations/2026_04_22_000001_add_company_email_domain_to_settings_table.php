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
        if (Schema::hasTable('settings') && ! Schema::hasColumn('settings', 'company_email_domain')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('company_email_domain')->default('tiptap.id')->after('jam_mulai_pulang');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'company_email_domain')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('company_email_domain');
            });
        }
    }
};
