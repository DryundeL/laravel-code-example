<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('setting_name')->nullable();
            $table->string('title')->nullable();
            $table->json('data')->nullable();
            $table->boolean('need_more_data')->default(false);
            $table->timestamps();
        });

        $notifications = [
            'message' => [
                'setting' => 'new_message',
            ],
            'account_blocked' => [
                'title' => 'Ваш аккаунт заблокирован',
                'setting' => 'block_status_changed',
                'text' => 'Блокировка профиля из-за задолженности',
            ],
            'account_unblocked' => [
                'title' => 'Ваш аккаунт разблокирован',
                'setting' => 'block_status_changed',
                'text' => 'Доступ к системе восстановлен',
            ],
            'canceled_lesson' => [
                'title' => 'Занятие отменено',
                'setting' => 'lesson_cancelled',
                'need_more_data' => true,
                'text' => 'По дисциплине ',
            ],
            'complete_document' => [
                'title' => 'Документ готов',
                'setting' => 'document_status_changed',
                'need_more_data' => true,
            ],
            'confirmation_document' => [
                'title' => 'Документ требует подтверждения',
                'setting' => 'document_status_changed',
                'need_more_data' => true,
            ],
            'elective' => [
                'title' => 'Вам доступна дисциплина на выбор',
                'setting' => 'elective_subject_added',
                'text' => 'Выберите новый предмет',
            ],
            'final_grade' => [
                'title' => 'Выставлена итоговая оценка',
                'setting' => 'final_grade_posted',
                'need_more_data' => true,
                'text' => 'По дисциплине ',
            ],
            'important_news' => [
                'title' => 'Важная новость',
                'need_more_data' => true,
            ],
            'new_materials' => [
                'title' => 'Добавлены новые материалы',
                'setting' => 'new_material_added',
                'need_more_data' => true,
                'text' => 'В дисциплину ',
            ],
            'opened_test' => [
                'title' => 'Открылся тест по дисциплине',
                'setting' => 'test_opened',
                'need_more_data' => true,
            ],
            'payment_debt' => [
                'title' => 'Напоминание об оплате',
                'setting' => 'payment_due',
                'need_more_data' => true,
                'text' => 'Вы просрочили оплату за ',
            ],
            'payment_reminder' => [
                'title' => 'Напоминание об оплате',
                'setting' => 'payment_due',
                'month' => [
                    'text' => 'До оплаты за семестр остался месяц'
                ],
                'week' => [
                    'text' => 'До оплаты за семестр осталась неделя'
                ],
                'day' => [
                    'text' => 'До оплаты за семестр остался всего 1 день'
                ],
            ],
            'personal_offer' => [
                'title' => 'Персональное предложение',
                'setting' => 'payment_due',
                'need_more_data' => true,
            ],
            'processed_document' => [
                'title' => 'Документ подан',
                'setting' => 'document_status_changed',
                'need_more_data' => true,
            ],
            'session_reminder' => [
                'title' => 'Скоро сессия',
                'setting' => 'session_warning',
                'month' => [
                    'text' => 'До начала остался месяц — проверьте расписание'
                ],
                'week' => [
                    'text' => 'До начала осталась всего лишь неделя, рекомендуем подготовиться'
                ],
                'day' => [
                    'text' => 'Завтра уже начало, надеемся вы подготовились'
                ],
            ],
            'test_reminder' => [
                'title' => 'Скоро зачеты',
                'setting' => 'test_warning',
                'month' => [
                    'text' => 'До начала остался месяц — проверьте график проведения'
                ],
                'week' => [
                    'text' => 'До начала зачётов осталась всего лишь неделя — рекомендуем завершить все незакрытые работы'
                ],
                'day' => [
                    'text' => 'Завтра стартует зачётная неделя — надеемся, вы всё подготовили!'
                ],
            ],
        ];

        DB::transaction(function () use ($notifications) {
            foreach ($notifications as $name => $info) {
                $setting = $info['setting'] ?? null;
                $title = $info['title'] ?? null;
                $needMoreData = $info['need_more_data'] ?? false;

                $data = collect($info)
                    ->except(['setting', 'title', 'need_more_data'])
                    ->toArray();

                DB::table('notifications')->insert([
                    'name' => $name,
                    'setting_name' => $setting,
                    'title' => $title,
                    'data' => !empty($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : null,
                    'need_more_data' => $needMoreData,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        Schema::create('notification_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');
            $table->string('text');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_profile');
        Schema::dropIfExists('notifications');
    }
};
