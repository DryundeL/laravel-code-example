<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\Access;
use App\Modules\User\Models\Module;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class ModuleService extends BaseService
{
    public function __construct(Module $module)
    {
        $this->setModel($module);
    }

    public function create(array $attributes): bool
    {
        return DB::transaction(function () use ($attributes) {
            $order = $attributes['order'] ?? null;
            $accesses = $attributes['accesses'] ?? [];

            $module = new Module($attributes);
            $module->save();

            if (empty($accesses)) {
                $accesses = Access::all()->pluck('id')->toArray();
            }

            foreach ($accesses as $accessId) {
                if ($order) {
                    DB::table('access_module')
                        ->where('access_id', $accessId)
                        ->where('order', '>=', $order)
                        ->increment('order');

                    $module->accesses()->attach($accessId, ['order' => $order]);
                } else {
                    $access = Access::find($accessId);

                    if ($access) {
                        $maxOrder = $access->modules()->max('order');
                        $newOrder = $maxOrder ? $maxOrder + 1 : 1;
                        $module->accesses()->attach($accessId, ['order' => $newOrder]);
                    }
                }
            }

            return true;
        });
    }

    public function swap(array $attributes, int $moduleId): bool
    {
        return DB::transaction(function () use ($attributes, $moduleId) {
            $newOrder = $attributes['order'];
            $accesses = $attributes['accesses'] ?? [];

            if (empty($accesses)) {
                $accesses = Access::all()->pluck('id')->toArray();
            }

            foreach ($accesses as $accessId) {
                $pivot = DB::table('access_module')
                    ->where('access_id', $accessId)
                    ->where('module_id', $moduleId)
                    ->first();

                if (!$pivot) {
                    continue;
                }

                $currentOrder = $pivot->order;

                if ($currentOrder === $newOrder) {
                    continue;
                }

                $otherModulePivot = DB::table('access_module')
                    ->where('access_id', $accessId)
                    ->where('order', $newOrder)
                    ->first();

                if ($otherModulePivot) {
                    DB::table('access_module')
                        ->where('access_id', $accessId)
                        ->where('module_id', $otherModulePivot->module_id)
                        ->update(['order' => $currentOrder]);
                }

                DB::table('access_module')
                    ->where('access_id', $accessId)
                    ->where('module_id', $moduleId)
                    ->update(['order' => $newOrder]);
            }

            return true;
        });
    }

    public function delete(array $attributes, int $moduleId): bool
    {
        $accesses = $attributes['accesses'] ?? [];

        $module = Module::find($moduleId);
        if (!$module) {
            return false;
        }

        if (empty($accesses)) {
            $moduleAccesses = $module->accesses()->get();

            foreach ($moduleAccesses as $access) {
                $removedOrder = $access->pivot->order;

                DB::table('access_module')
                    ->where('access_id', $access->id)
                    ->where('order', '>', $removedOrder)
                    ->decrement('order');
            }

            $module->accesses()->detach();
            $module->delete();
        } else {
            foreach ($accesses as $accessId) {
                $pivot = DB::table('access_module')
                    ->where('access_id', $accessId)
                    ->where('module_id', $moduleId)
                    ->first();

                if ($pivot) {
                    $removedOrder = $pivot->order;

                    DB::table('access_module')
                        ->where('access_id', $accessId)
                        ->where('order', '>', $removedOrder)
                        ->decrement('order');

                    $module->accesses()->detach($accessId);
                }
            }
        }

        return true;
    }
}
