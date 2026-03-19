<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_failed_deliveries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('notification_id')->nullable()->constrained('system_notifications')->nullOnDelete();
            $table->string('channel', 32);
            $table->string('provider', 64)->nullable();
            $table->text('error_message')->nullable();
            $table->json('payload')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->boolean('retry_pending')->default(false);
            $table->timestamps();

            $table->index(['channel', 'retry_pending'], 'notif_failed_channel_retry_idx');
            $table->index('created_at', 'notif_failed_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_failed_deliveries');
    }
};
