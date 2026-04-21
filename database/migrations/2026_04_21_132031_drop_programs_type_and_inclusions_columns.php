<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            if (Schema::hasColumn('programs', 'type')) {
                $table->dropColumn('type');
            }

            if (Schema::hasColumn('programs', 'inclusions')) {
                $table->dropColumn('inclusions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            if (! Schema::hasColumn('programs', 'type')) {
                $table->string('type', 20)->default('program')->index();
            }

            if (! Schema::hasColumn('programs', 'inclusions')) {
                $table->json('inclusions')->default('[]');
            }
        });
    }
};
