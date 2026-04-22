<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('enrollments', 'facebook') && ! Schema::hasColumn('enrollments', 'facebook_messenger_name')) {
            $driver = DB::getDriverName();

            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `enrollments` RENAME COLUMN `facebook` TO `facebook_messenger_name`');
            } elseif ($driver === 'pgsql') {
                DB::statement('ALTER TABLE "enrollments" RENAME COLUMN "facebook" TO "facebook_messenger_name"');
            } elseif ($driver === 'sqlite') {
                // SQLite column rename is supported in modern SQLite.
                DB::statement('ALTER TABLE "enrollments" RENAME COLUMN "facebook" TO "facebook_messenger_name"');
            } else {
                throw new \RuntimeException("Unsupported database driver for renaming columns: {$driver}");
            }
        }

        if (! Schema::hasColumn('enrollments', 'facebook_messenger_url')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->string('facebook_messenger_url')->nullable()->after('facebook_messenger_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('enrollments', 'facebook_messenger_url')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropColumn('facebook_messenger_url');
            });
        }

        if (Schema::hasColumn('enrollments', 'facebook_messenger_name') && ! Schema::hasColumn('enrollments', 'facebook')) {
            $driver = DB::getDriverName();

            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `enrollments` RENAME COLUMN `facebook_messenger_name` TO `facebook`');
            } elseif ($driver === 'pgsql') {
                DB::statement('ALTER TABLE "enrollments" RENAME COLUMN "facebook_messenger_name" TO "facebook"');
            } elseif ($driver === 'sqlite') {
                DB::statement('ALTER TABLE "enrollments" RENAME COLUMN "facebook_messenger_name" TO "facebook"');
            } else {
                throw new \RuntimeException("Unsupported database driver for renaming columns: {$driver}");
            }
        }
    }
};
