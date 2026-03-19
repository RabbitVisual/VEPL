<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('notification_id')->constrained('system_notifications')->cascadeOnDelete();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'notification_id'], 'user_notif_unique_delivery');
            $table->index(['user_id', 'is_read'], 'user_notif_user_read_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
