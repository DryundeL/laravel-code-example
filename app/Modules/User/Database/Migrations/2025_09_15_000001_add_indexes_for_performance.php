<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Добавляем индексы для таблицы profiles
        Schema::table('profiles', function (Blueprint $table) {
            $table->index('external_id');
            $table->index('user_id');
            $table->index('ban');
            $table->index(['user_id', 'ban']); // Составной индекс для запроса профилей пользователя
        });

        // Добавляем индексы для таблицы users
        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropIndex(['external_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['ban']);
            $table->dropIndex(['user_id', 'ban']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
        });
    }
};
