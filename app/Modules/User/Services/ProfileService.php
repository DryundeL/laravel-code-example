<?php

namespace App\Modules\User\Services;

use App\Modules\User\Interfaces\DataTransfer\ProfileServiceInterface;
use App\Modules\User\Models\Profile;
use App\Services\BaseService;

class ProfileService extends BaseService implements ProfileServiceInterface
{
    public function __construct(Profile $profile)
    {
        $this->setModel($profile);
    }

    /**
     * @param array $attributes
     * @return Profile
     */
    public function createProfile(array $attributes): Profile
    {
        $profile = new Profile($attributes);

        return $profile;
    }

    public function findProfileByExternalId(int $externalId)
    {
        return $this->model->where('external_id', $externalId)->first();
    }

    public function findProfileById(int $id)
    {
        return $this->find($id);
    }

    public function getAllProfiles(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->all();
    }

    /**
     * Получить общее количество профилей
     *
     * @return int
     */
    public function getTotalProfilesCount(): int
    {
        return $this->model->count();
    }

    /**
     * Получить статистику по группам
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getGroupStatistics(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->select('group', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->whereNotNull('group')
            ->groupBy('group')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Получить статистику по организациям
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrgStatistics(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->select('org', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->whereNotNull('org')
            ->groupBy('org')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Получить количество профилей с долгами
     *
     * @return int
     */
    public function getProfilesWithDebtCount(): int
    {
        return $this->model->whereNotNull('last_debt')
            ->where('last_debt', '>', 0)
            ->count();
    }

    /**
     * Получить общую сумму долгов
     *
     * @return float|int
     */
    public function getTotalDebtAmount(): float|int
    {
        return $this->model->whereNotNull('last_debt')
            ->where('last_debt', '>', 0)
            ->sum('last_debt');
    }

    /**
     * Получить статистику по долгам по группам
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDebtByGroups(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->select(
                'group',
                \Illuminate\Support\Facades\DB::raw('count(*) as count'),
                \Illuminate\Support\Facades\DB::raw('sum(last_debt) as total_debt')
            )
            ->whereNotNull('last_debt')
            ->where('last_debt', '>', 0)
            ->whereNotNull('group')
            ->groupBy('group')
            ->orderBy('count', 'desc')
            ->get();
    }
}
