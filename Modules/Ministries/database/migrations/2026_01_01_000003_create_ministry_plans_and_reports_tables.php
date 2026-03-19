<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ministry_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained('ministries')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedSmallInteger('period_year');
            $table->enum('period_type', ['annual', 'semiannual', 'quarterly', 'monthly'])->default('annual');
            $table->date('period_start');
            $table->date('period_end');
            $table->text('objectives')->nullable();
            $table->json('goals')->nullable();
            $table->json('activities')->nullable();
            $table->decimal('budget_requested', 14, 2)->nullable();
            $table->text('budget_notes')->nullable();
            $table->enum('status', ['draft', 'under_admin_review', 'approved', 'in_execution', 'archived'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('approval_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['ministry_id', 'period_year', 'period_type'], 'ministry_plans_period_idx');
            $table->index('status', 'ministry_plans_status_idx');
        });

        Schema::create('ministry_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained('ministries')->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('ministry_plans')->nullOnDelete();
            $table->unsignedSmallInteger('report_year');
            $table->unsignedTinyInteger('report_month');
            $table->date('period_start');
            $table->date('period_end');
            $table->json('quantitative_data')->nullable();
            $table->text('qualitative_summary')->nullable();
            $table->text('prayer_requests')->nullable();
            $table->text('highlights')->nullable();
            $table->text('challenges')->nullable();
            $table->enum('status', ['draft', 'submitted', 'under_admin_review', 'archived'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('treasury_summary')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->unique(['ministry_id', 'report_year', 'report_month'], 'ministry_reports_unique_period');
            $table->index('status', 'ministry_reports_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ministry_reports');
        Schema::dropIfExists('ministry_plans');
    }
};
