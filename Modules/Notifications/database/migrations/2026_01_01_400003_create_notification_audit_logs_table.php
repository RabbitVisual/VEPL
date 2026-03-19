<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_audit_logs', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('channel', 32);
            $table->string('status', 32);
            $table->foreignId('notification_id')->nullable()->constrained('system_notifications')->nullOnDelete();
            $table->json('payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'channel', 'status'], 'notif_audit_user_channel_status_idx');
            $table->index('created_at', 'notif_audit_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_audit_logs');
    }
};
