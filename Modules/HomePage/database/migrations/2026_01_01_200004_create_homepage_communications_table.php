<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('ministerial_interest', 80)->nullable();
            $table->string('segment', 40)->default('general');
            $table->string('preferred_frequency', 30)->default('weekly');
            $table->json('tags')->nullable();
            $table->json('engagement_data')->nullable();
            $table->timestamp('last_opened_at')->nullable();
            $table->timestamp('last_clicked_at')->nullable();
            $table->unsignedInteger('lead_score')->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('confirmation_token')->nullable();
            $table->boolean('is_confirmed')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'is_confirmed'], 'home_newsletter_status_idx');
            $table->index(['segment', 'ministerial_interest'], 'home_newsletter_segment_idx');
            $table->index('lead_score', 'home_newsletter_lead_score_idx');
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->text('message');

            $table->string('inquiry_type', 40)->default('general');
            $table->string('ministerial_context')->nullable();
            $table->string('interest_area')->nullable();
            $table->unsignedInteger('lead_score')->default(0);
            $table->json('lead_scoring')->nullable();
            $table->timestamp('follow_up_scheduled')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->string('status', 30)->default('new');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'read_at'], 'home_contact_status_read_idx');
            $table->index(['inquiry_type', 'ministerial_context'], 'home_contact_inquiry_idx');
            $table->index('follow_up_scheduled', 'home_contact_follow_up_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('newsletter_subscribers');
    }
};
