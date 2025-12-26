<?php

namespace App\Modules\User\Interfaces\DataTransfer;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{
    public function createUser(array $attributes);
    public function findUserByEmail(string $email): User|null;
    public function findUserById(int $id): User|null;

    // Методы для статистики
    public function getTotalUsersCount(): int;
    public function getUsersWithProfilesCount(): int;
    public function getNewUsersCountSince(\Carbon\Carbon $date): int;
    public function getWeeklyActivity(): Collection;
    public function getUsersList(array $filters): LengthAwarePaginator;
}
