<?php

namespace App\Modules\User\Interfaces\DataTransfer;

use Illuminate\Database\Eloquent\Collection;

interface ProfileServiceInterface
{
    public function createProfile(array $attributes);

    public function findProfileByExternalId(int $externalId);

    public function findProfileById(int $id);
    public function getAllProfiles();

    // Методы для статистики
    public function getTotalProfilesCount(): int;
    public function getGroupStatistics(): Collection;
    public function getOrgStatistics(): Collection;
    public function getProfilesWithDebtCount(): int;
    public function getTotalDebtAmount(): float|int;
    public function getDebtByGroups(): Collection;
}
