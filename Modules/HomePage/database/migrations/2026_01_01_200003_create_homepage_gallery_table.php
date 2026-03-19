<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_url')->nullable();

            $table->string('category', 80)->nullable();
            $table->string('content_type', 40)->default('image');
            $table->string('formation_context')->nullable();
            $table->date('captured_at')->nullable();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->unsignedBigInteger('sermon_id')->nullable();
            $table->json('educational_metadata')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_featured')->default(false);

            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'order'], 'home_gallery_active_order_idx');
            $table->index(['category', 'is_active'], 'home_gallery_category_active_idx');
            $table->index(['event_id', 'sermon_id'], 'home_gallery_content_ref_idx');
            $table->index(['is_featured', 'captured_at'], 'home_gallery_featured_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
    }
};
