<?php

use App\Modules\User\Models\Access;
use App\Modules\Widget\Models\Widget;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->boolean('inuse')->default(false)->change();
        });

        Schema::create('widgets', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->boolean('inuse')->default(false);

            $table->timestamps();
        });

        Schema::create('access_widget', function (Blueprint $table) {
            $table->id();

            $table->foreignId('access_id')->constrained('accesses')->onDelete('cascade');
            $table->foreignId('widget_id')->constrained('widgets')->onDelete('cascade');
            $table->integer('order');

            $table->timestamps();
        });

        $widgetAttr = [
            [
                'name' => 'welcome',
                'inuse' => true,
            ],
            [
                'name' => 'schedule',
                'inuse' => true,
            ],
            [
                'name' => 'announce',
                'inuse' => true,
            ],
            [
                'name' => 'finance',
                'inuse' => true,
            ],
            [
                'name' => 'certification',
                'inuse' => true,
            ],
            [
                'name' => 'support',
                'inuse' => true,
            ],
        ];

        foreach ($widgetAttr as $attr) {
            Widget::create($attr);
        }

        $accesses = Access::all();
        $widgets = Widget::all();

        foreach ($accesses as $access) {
            $order = 1;
            foreach ($widgets as $widget) {
                $access->widgets()->attach($widget->id, ['order' => $order++]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->boolean('inuse')->default(true)->change();
        });
        Schema::dropIfExists('access_widget');
        Schema::dropIfExists('widgets');
    }
};
