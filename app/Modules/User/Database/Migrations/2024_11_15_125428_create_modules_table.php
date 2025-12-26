<?php

use App\Modules\User\Models\Access;
use App\Modules\User\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('desc');
            $table->boolean('icon')->default(true);
            $table->boolean('show')->default(true);
            $table->boolean('inuse')->default(true);

            $table->timestamps();
        });

        Schema::create('accesses', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->timestamps();
        });

        Schema::create('access_module', function (Blueprint $table) {
            $table->id();

            $table->foreignId('access_id')->constrained('accesses')->onDelete('cascade');
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->integer('order');

            $table->timestamps();
        });

        $accessAttr = [
            [
                'name' => 'Студент (дистант)',
            ],
            [
                'name' => 'Студент',
            ],
        ];

        foreach ($accessAttr as $attr) {
            Access::create($attr);
        }

        $moduleAttr = [
            [
                'name' => 'education',
                'desc' => 'Обучение',
            ],
            [
                'name' => 'schedule',
                'desc' => 'Расписание',
            ],
            [
                'name' => 'assist',
                'desc' => 'Помощь',
                'icon' => false,
                'show' => false,
            ],
        ];

        foreach ($moduleAttr as $attr) {
            Module::create($attr);
        }

        $accesses = Access::all();
        $modules = Module::all();

        foreach ($accesses as $access) {
            $order = 1;
            foreach ($modules as $module) {
                $access->modules()->attach($module->id, ['order' => $order++]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('access_module');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('accesses');
    }
};
