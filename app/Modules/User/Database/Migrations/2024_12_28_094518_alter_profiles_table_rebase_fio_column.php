<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('photo_url')->nullable();
        });

        // 2. Переносим данные ФИО из profiles в users с использованием сырых SQL-запросов для PostgreSQL
        DB::statement('
            UPDATE users
            SET
                first_name = profiles.first_name,
                last_name = profiles.last_name,
                middle_name = profiles.middle_name,
                photo_url = profiles.photo_url
            FROM profiles
            WHERE profiles.user_id = users.id
        ');

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('middle_name');
            $table->dropColumn('photo_url');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('photo_url')->nullable();
        });

        // 2. Переносим данные ФИО из profiles в users с использованием сырых SQL-запросов для PostgreSQL
        DB::statement('
            UPDATE profiles
            SET
                first_name = users.first_name,
                last_name = users.last_name,
                middle_name = users.middle_name,
                photo_url = users.photo_url
            FROM users
            WHERE profiles.user_id = users.id
        ');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('middle_name');
            $table->dropColumn('photo_url');
        });
    }
};
