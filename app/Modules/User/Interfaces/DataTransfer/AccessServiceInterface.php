<?php

namespace App\Modules\User\Interfaces\DataTransfer;

use Illuminate\Support\Collection;

interface AccessServiceInterface
{
    public function findAccessById(int $id);

    public function getAll(): Collection;

    // Методы для статистики
    public function getAccessStatistics(): Collection;
}
