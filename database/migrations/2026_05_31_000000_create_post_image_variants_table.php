<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('post_image_variants')) {
            return;
        }

        Schema::create('post_image_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_image_id')->constrained()->cascadeOnDelete();
            $table->string('variant', 30);
            $table->string('disk', 50);
            $table->string('path', 500);
            $table->string('url', 1000)->nullable();
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->timestamps();

            $table->unique(['post_image_id', 'variant']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_image_variants');
    }
};
