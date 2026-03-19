<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('photo')->nullable();
            $table->text('testimonial');
            $table->string('position')->nullable();

            $table->string('ministerial_title')->nullable();
            $table->string('formation_completed')->nullable();
            $table->string('church_affiliation')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('testimonial_type', 40)->default('written');
            $table->unsignedTinyInteger('impact_score')->nullable();
            $table->string('ministry_level', 40)->nullable();
            $table->string('video_url')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->json('verification_data')->nullable();

            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'order'], 'home_testimonial_active_order_idx');
            $table->index(['is_featured', 'is_active'], 'home_testimonial_featured_idx');
            $table->index(['ministerial_title', 'formation_completed'], 'home_testimonial_ministry_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
