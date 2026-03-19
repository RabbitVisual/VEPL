<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('notification_type', 64);
            $table->json('channels');
            $table->time('dnd_from')->nullable();
            $table->time('dnd_to')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'notification_type'], 'user_notif_pref_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notification_preferences');
    }
};
