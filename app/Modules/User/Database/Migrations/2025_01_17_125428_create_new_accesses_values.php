<?php

use App\Modules\User\Models\Access;
use App\Modules\User\Models\Module;
use App\Modules\Widget\Models\Widget;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        $accessAttr = [
            'id' => 20,
            'name' => 'Абитуриент',
        ];

        $access = new Access($accessAttr);
        $access->save();

        $widgets = Widget::all();
        $modules = Module::all();

        $order = 1;
        foreach ($widgets as $widget) {
            $access->widgets()->attach($widget->id, ['order' => $order++]);
        }

        $order = 1;
        foreach ($modules as $module) {
            $access->modules()->attach($module->id, ['order' => $order++]);
        }
    }

    public function down(): void
    {
    }
};
