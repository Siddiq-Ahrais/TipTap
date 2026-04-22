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
        Schema::table('attendances', function (Blueprint $table): void {
            $table->string('early_checkout_status')->nullable()->after('status');
            $table->timestamp('early_checkout_requested_at')->nullable()->after('early_checkout_status');
            $table->timestamp('early_checkout_reviewed_at')->nullable()->after('early_checkout_requested_at');
            $table->foreignId('early_checkout_reviewed_by')->nullable()->after('early_checkout_reviewed_at')->constrained('users')->nullOnDelete();
            $table->text('early_checkout_note')->nullable()->after('early_checkout_reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('early_checkout_reviewed_by');
            $table->dropColumn([
                'early_checkout_status',
                'early_checkout_requested_at',
                'early_checkout_reviewed_at',
                'early_checkout_note',
            ]);
        });
    }
};
