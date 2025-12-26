<?php

use App\Modules\Story\Models\Story;
use App\Modules\User\Models\Access;
use App\Modules\User\Models\Module;
use App\Modules\Widget\Models\Widget;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        $accessAttr = [
            [
                'id' => 5,
                'name' => 'Тест',
            ],
            [
                'id' => 6,
                'name' => 'Должник',
            ],
            [
                'id' => 9,
                'name' => 'Временный доступ',
            ],
        ];

        $newAccesses = collect();
        foreach ($accessAttr as $attr) {
            $newAccesses->push(Access::create($attr));
        }

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

        $allModules = Module::all()->keyBy('name');
        $orderedModules = collect();

        foreach ($desiredOrder as $name) {
            if ($allModules->has($name)) {
                $orderedModules->push($allModules->get($name));
                $allModules->forget($name);
            }
        }

        if ($allModules->isNotEmpty()) {
            $remainingModules = $allModules->sortBy('id');
            $orderedModules = $orderedModules->merge($remainingModules);
        }

        foreach ($newAccesses as $access) {
            $order = 1;
            foreach ($orderedModules as $module) {
                $access->modules()->attach($module->id, ['order' => $order++]);
            }
        }

        $widgets = Widget::all();

        foreach ($newAccesses as $access) {
            $order = 1;
            foreach ($widgets as $widget) {
                $access->widgets()->attach($widget->id, ['order' => $order++]);
            }
        }

        $stories = Story::all();

        foreach ($newAccesses as $access) {
            foreach ($stories as $story) {
                $access->stories()->attach($story->id);
            }
        }
    }

    public function down(): void
    {
        Access::whereIn('id', [5, 6, 9])->delete();
    }
};
