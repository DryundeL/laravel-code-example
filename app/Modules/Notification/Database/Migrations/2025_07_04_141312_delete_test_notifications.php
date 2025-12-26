<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::delete("DELETE FROM notifications WHERE name LIKE 'test_reminder'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::transaction(function () {
            DB::table('notifications')->insert([
                'name' => 'test_reminder',
                'setting_name' => 'test_warning',
                'title' => 'Скоро зачеты',
                'data' => json_encode([
                    'month' => [
                        'text' => 'До начала остался месяц — проверьте график проведения'
                    ],
                    'week' => [
                        'text' => 'До начала зачётов осталась всего лишь неделя — рекомендуем завершить все незакрытые работы'
                    ],
                    'day' => [
                        'text' => 'Завтра стартует зачётная неделя — надеемся, вы всё подготовили!'
                    ],
                ], JSON_UNESCAPED_UNICODE),
                'need_more_data' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
};
