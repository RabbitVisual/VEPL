<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ministries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color', 30)->default('blue');
            $table->foreignId('leader_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('co_leader_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_approval')->default(false);
            $table->unsignedInteger('max_members')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'name'], 'ministries_active_name_idx');
            $table->index(['leader_id', 'co_leader_id'], 'ministries_leaders_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ministries');
    }
};
