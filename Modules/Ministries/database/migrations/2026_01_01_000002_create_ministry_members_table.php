<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ministry_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained('ministries')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role', ['member', 'leader', 'co_leader', 'coordinator'])->default('member');
            $table->enum('status', ['pending', 'active', 'inactive', 'removed'])->default('pending');
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('left_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['ministry_id', 'user_id'], 'ministry_members_unique_member');
            $table->index(['status', 'joined_at'], 'ministry_members_status_joined_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ministry_members');
    }
};
