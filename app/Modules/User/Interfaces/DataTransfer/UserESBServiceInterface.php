<?php
namespace App\Modules\User\Interfaces\DataTransfer;

use App\Models\User;

interface UserESBServiceInterface
{
    public function getProfilesFromESB(User $user): array;
    public function setAdditionalInfoForProfilesToRedis(array $profiles): void;
    public function setProfilesFromESB(User $user, array $profiles): array|bool;
}
