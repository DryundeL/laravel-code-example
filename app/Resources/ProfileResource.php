<?php

namespace App\Resources;

use App\Modules\User\Http\Resources\ModuleResource;
use App\Services\ProfileDataService;
use Illuminate\Http\Request;
use function App\Helpers\getDebtLevel;
use Carbon\Carbon;

class ProfileResource extends BaseResource
{
    private array $alwaysAllowedModules = [
        'finances',
        'documents',
        'curator',
        'chats',
        'pass'
    ];

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'profile';

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $profileData = $this->getProfileData();

        $access = $this->access;

        if ($access->relationLoaded('modules')) {
            $modules = $access->modules->filter(fn($module) => $module->inuse)->values();
        } else {
            $modules = $access->modules()->findInUses()->get();
        }

        if ($access->relationLoaded('widgets')) {
            $widgetsData = $access->widgets
                ->filter(fn($widget) => $widget->inuse)
                ->sortBy('pivot.order')
                ->values();
        } else {
            $widgetsData = $access->widgets()->select('name')->findInUses()->orderBy('widget_id')->get();
        }

        $widgets = [];
        $widgetsData->each(function ($widget) use (&$widgets) {
            $widgets[] = $widget->name;
        });

        $modulesCollection = ModuleResource::collection($modules);
        $physCode = $profileData['physCode'] ?? (string)$this->external_id;

        if ($profileData['fullAccessEndDate']?->greaterThanOrEqualTo(Carbon::now()->format('Y-m-d'))) {
            $modulesCollection->each(function ($module) {
                $module->additional(['isBlocked' => false]);
            });

            return array_merge(parent::toArray($request), [
                'access' => $access->name,
                'group' => $this->group,
                'spec_name' => $profileData['specName'],
                'edu_form' => $profileData['eduForm'],
                'faculty' => $profileData['faculty'],
                'program' => $profileData['program'],
                'phys_code' => $physCode,
                'education_profile' => $profileData['educationProfile'],
                'level' => $profileData['level'],
                'org' => $this->org,
                'modules' => $modulesCollection,
                'widgets' => $widgets,
                'debt_lvl' => 0,
                'modules_unlocked' => false,
            ]);
        }

        $debtLvl = getDebtLevel($this->debt ?? 0);

        $modulesCollection = $modulesCollection->map(function ($module) use ($debtLvl) {
            $isBlocked = $this->isModuleBlocked($module->name, $debtLvl);
            return $module->additional(['isBlocked' => $isBlocked]);
        });

        $lastDebtLvl = getDebtLevel($this->last_debt ?? 0);
        $modulesUnlocked = $debtLvl < $lastDebtLvl;

        return array_merge(parent::toArray($request), [
            'access' => $access->name,
            'group' => $this->group,
            'spec_name' => $profileData['specName'],
            'edu_form' => $profileData['eduForm'],
            'faculty' => $profileData['faculty'],
            'program' => $profileData['program'],
            'phys_code' => $physCode,
            'education_profile' => $profileData['educationProfile'],
            'level' => $profileData['level'],
            'org' => $this->org,
            'modules' => $modulesCollection,
            'widgets' => $widgets,
            'debt_lvl' => $debtLvl,
            'modules_unlocked' => $modulesUnlocked,
        ]);

    }

    private function isModuleBlocked(string $moduleName, int $debtLvl): bool
    {
        return match ($debtLvl) {
            3 => !in_array($moduleName, $this->alwaysAllowedModules, true),
            1, 2 => $moduleName === 'library',
            default => false,
        };
    }

    /**
     * Получает данные профиля через сервис
     *
     * @return array
     */
    private function getProfileData(): array
    {
        $profileDataService = app(ProfileDataService::class);
        return $profileDataService->getProfileData($this->resource);
    }
}
