<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->default('Review Packages');
            $table->string('tag')->nullable();

            $table->unsignedInteger('price_full');
            $table->unsignedInteger('price_dp');
            $table->unsignedInteger('price_early')->nullable();
            $table->date('early_deadline')->nullable();

            $table->text('early_bird_label')->nullable();
            $table->json('inclusions');

            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
