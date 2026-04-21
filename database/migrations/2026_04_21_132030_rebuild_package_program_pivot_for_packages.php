<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('package_program');

        Schema::create('package_program', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('programs')->restrictOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['package_id', 'program_id']);
            $table->index(['program_id', 'package_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_program');
    }
};
