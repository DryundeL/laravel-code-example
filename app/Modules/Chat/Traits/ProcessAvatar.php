<?php

namespace App\Modules\Chat\Traits;

use App\Modules\User\Interfaces\DataTransfer\ProfileServiceInterface;

trait ProcessAvatar
{
    public ?ProfileServiceInterface $profileService = null;

    public function __construct()
    {
        $this->profileService = app(ProfileServiceInterface::class);
    }

    public function processAvatar(array &$data): void
    {
        if ($data['role'] !== 'Одногруппник' && $data['role'] !== 'Студент') {
            $data['photo_url'] = $data['avatar'];
        } elseif ($this->profileService->findProfileByExternalId($data['id'])) {
            $profile = $this->profileService->findProfileByExternalId($data['id']);
            $photoUrl = $profile->user->photo_url;
            $data['photo_url'] = $photoUrl;
        } else {
            $data['photo_url'] = null;
        }

        unset($data['avatar']);
    }
}
