<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tag')->nullable();

            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('category')->default('Review Packages');

            $table->unsignedInteger('price_full');
            $table->unsignedInteger('price_dp');
            $table->unsignedInteger('price_early')->nullable();
            $table->date('early_deadline')->nullable();
            $table->text('early_bird_label')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
