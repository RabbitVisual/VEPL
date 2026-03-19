<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['info', 'success', 'warning', 'error', 'achievement'])->default('info');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->json('target_users')->nullable();
            $table->json('target_roles')->nullable();
            $table->json('target_ministries')->nullable();
            $table->string('action_url')->nullable();
            $table->string('action_text')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_read')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('notification_type', 64)->nullable();
            $table->unsignedInteger('group_count')->default(1);
            $table->timestamps();

            $table->index(['type', 'priority'], 'sys_notif_type_priority_idx');
            $table->index(['notification_type', 'created_at'], 'sys_notif_category_created_idx');
            $table->index('scheduled_at', 'sys_notif_scheduled_at_idx');
            $table->index('expires_at', 'sys_notif_expires_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_notifications');
    }
};
