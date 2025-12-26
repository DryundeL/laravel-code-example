<?php

namespace App\Modules\User\Services;

use App\Modules\User\Interfaces\DataTransfer\AccessServiceInterface;
use App\Modules\User\Models\Access;
use App\Services\BaseService;
use Illuminate\Support\Collection;

class AccessService extends BaseService implements AccessServiceInterface
{
    public function __construct(Access $access)
    {
        $this->setModel($access);
    }

    public function findAccessById(int $id)
    {
        return $this->find($id);
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * Получить статистику по уровням доступа
     *
     * @return Collection
     */
    public function getAccessStatistics(): Collection
    {
        return $this->model->withCount('profiles')
            ->orderBy('profiles_count', 'desc')
            ->get();
    }
}
