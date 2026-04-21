<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'program_id')) {
                $table->dropConstrainedForeignId('program_id');
            }

            if (Schema::hasColumn('enrollments', 'schedule_id')) {
                $table->dropConstrainedForeignId('schedule_id');
            }

            $table->nullableMorphs('purchasable');
            $table->string('purchasable_name_snapshot')->nullable()->after('purchasable_id');
            $table->string('purchasable_slug_snapshot')->nullable()->after('purchasable_name_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropMorphs('purchasable');
            $table->dropColumn(['purchasable_name_snapshot', 'purchasable_slug_snapshot']);
        });
    }
};
