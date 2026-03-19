<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carousel_slides', function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('alt_text')->nullable();

            $table->string('link')->nullable();
            $table->string('link_text')->nullable();

            $table->string('text_position', 30)->default('center');
            $table->string('text_alignment', 20)->default('center');
            $table->string('text_color', 7)->default('#ffffff');
            $table->string('overlay_color', 7)->default('#000000');
            $table->unsignedTinyInteger('overlay_opacity')->default(50);
            $table->string('button_style', 20)->default('primary');
            $table->string('logo_position', 30)->default('top_center');
            $table->unsignedSmallInteger('logo_scale')->default(100);

            $table->string('slide_type', 40)->default('hero');
            $table->string('target_audience', 40)->default('all');
            $table->string('campaign_tag')->nullable();
            $table->json('educational_content')->nullable();
            $table->json('analytics_data')->nullable();

            $table->string('transition_type', 20)->default('fade');
            $table->unsignedSmallInteger('transition_duration')->default(500);
            $table->boolean('show_indicators')->default(true);
            $table->boolean('show_controls')->default(true);

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'order'], 'home_carousel_active_order_idx');
            $table->index(['starts_at', 'ends_at'], 'home_carousel_schedule_idx');
            $table->index(['target_audience', 'slide_type'], 'home_carousel_audience_type_idx');
            $table->index('campaign_tag', 'home_carousel_campaign_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carousel_slides');
    }
};
