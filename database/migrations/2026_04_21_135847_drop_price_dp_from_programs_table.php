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
            if (Schema::hasColumn('programs', 'price_dp')) {
                $table->dropColumn('price_dp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            if (! Schema::hasColumn('programs', 'price_dp')) {
                $table->unsignedInteger('price_dp')->after('price_full');
            }
        });
    }
};
