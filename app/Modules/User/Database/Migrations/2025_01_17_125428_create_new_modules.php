<?php

use App\Modules\User\Models\Access;
use App\Modules\User\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        $moduleAttr = [
            [
                'name' => 'pass',
                'desc' => 'Пропуск',
                'icon' => true,
                'show' => true,
                'inuse' => true
            ],
            [
                'name' => 'finances',
                'desc' => 'Финансы',
                'icon' => true,
                'show' => true,
                'inuse' => true
            ],
            [
                'name' => 'announces',
                'desc' => 'Объявления',
                'icon' => true,
                'show' => true,
                'inuse' => true
            ],
            [
                'name' => 'portfolio',
                'desc' => 'Портфолио',
                'icon' => true,
                'show' => true,
                'inuse' => true
            ],
            [
                'name' => 'library',
                'desc' => 'Электронная библиотека',
                'icon' => true,
                'show' => true,
                'inuse' => true
            ],
            [
                'name' => 'chats',
                'desc' => 'Сообщения',
                'icon' => true,
                'show' => true,
                'inuse' => true
            ],
        ];

        foreach ($moduleAttr as $attr) {
            Module::create($attr);
        }

        // Задаём нужный порядок по имени модуля.
        $desiredOrder = [
            'announces',
            'schedule',
            'education',
            'portfolio',
            'library',
            'pass',
            'finance',
            'messages',
            'assist'
        ];

        // Получаем все модули и группируем по имени для удобного поиска
        $allModules = Module::all()->keyBy('name');

        // Собираем модули в нужном порядке
        $orderedModules = collect();

        foreach ($desiredOrder as $name) {
            if ($allModules->has($name)) {
                $orderedModules->push($allModules->get($name));
                // Если модуль найден, удаляем его из общего списка
                $allModules->forget($name);
            }
        }

        // Если остались модули, которых нет в desiredOrder, можно добавить их в конец.
        if ($allModules->isNotEmpty()) {
            // Здесь можно задать сортировку для оставшихся, например, по id
            $remainingModules = $allModules->sortBy('id');
            $orderedModules = $orderedModules->merge($remainingModules);
        }

        // Теперь для каждого доступа сбрасываем связи и прикрепляем модули в нужном порядке
        $accesses = Access::all();

        foreach ($accesses as $access) {
            // Сброс всех модулей, связанных с данным доступом
            $access->modules()->detach();

            $order = 1;
            foreach ($orderedModules as $module) {
                $access->modules()->attach($module, ['order' => $order++]);
            }
        }
    }

    public function down(): void
    {
    }
};
