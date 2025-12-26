<?php
namespace App\Modules\User\Services;

use App\Jobs\MailSendJob;
use App\Models\User;
use App\Modules\Auth\Mail\WelcomeMail;
use App\Modules\User\Interfaces\DataTransfer\AccessServiceInterface;
use App\Modules\User\Interfaces\DataTransfer\ProfileServiceInterface;
use App\Modules\User\Interfaces\DataTransfer\UserESBServiceInterface;
use App\Services\BaseService;
use App\Traits\LazyServiceLoader;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use JsonException;
use Psr\SimpleCache\InvalidArgumentException;

class UserESBService extends BaseService implements UserESBServiceInterface
{
    use LazyServiceLoader;

    /**
     */
    public function getProfilesFromESB(User $user): array
    {
        $attributes['email'] = $user->email;
        $payloadData = $this->prepareDataToESB(false, attributes: $attributes);

        $profiles = $this->handleRequestToESB('getProfile', $payloadData);

        if (isset($profiles['profiles']) && is_array($profiles['profiles'])) {
            foreach ($profiles['profiles'] as &$profile) {
                $profile['org'] = $profiles['org'];
            }

            unset($profile);
            $profiles = $profiles['profiles'];
        } else {
            return $this->getErrorMessage('profile', 'Профили не найдены');
        }

        return $profiles;
    }

    /**
     * @throws InvalidArgumentException
     * @throws JsonException
     */
    public function setAdditionalInfoForProfilesToRedis(array $profiles): void
    {
        foreach ($profiles as $profile) {
            if (!isset($profile['id'])) {
                continue;
            }

            $externalProfileId = $profile['id'];
            unset($profile['id'], $profile['name'], $profile['group'], $profile['access'], $profile['org'], $profile['ban'], $profile['gid']);
            $additionalInfo = json_encode($profile, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            Cache::set('external_profile_' . $externalProfileId, $additionalInfo);
        }
    }

    /**
     * Устанавливает профили пользователя из данных ESB
     *
     * @param User $user
     * @param array $profiles
     * @return array|bool
     * @throws \Throwable
     */
    public function setProfilesFromESB(User $user, array $profiles): array|bool
    {
        if (empty($profiles)) {
            return $this->getErrorMessage('profiles', 'Профили не найдены. Напишите в поддержку: techbug@inpsycho.ru');
        }

        $userAttr = [];
        $hasNewProfile = false;

        foreach ($profiles as $profile) {
            $externalId = $profile['id'];
            $FIO = explode(' ', $profile['name']);

            $userAttr = [
                'first_name' => $FIO[1],
                'last_name' => $FIO[0],
                'middle_name' => $FIO[2] ?? null,
            ];

            $attributes = [
                'external_id' => $externalId,
                'group' => $profile['group'],
                'org' => $profile['org'],
                'ban' => $profile['ban'],
                'gid' => $profile['gid'],
            ];

            $accessService = $this->getService(AccessServiceInterface::class);
            if (!$accessService->findAccessById($profile['access'])) {
                continue;
            }

            $profileService = $this->getService(ProfileServiceInterface::class);
            $profileExist = $profileService->findProfileByExternalId($externalId);

            if ($profileExist) {
                DB::transaction(function () use ($profileExist, $profile, $attributes) {
                    $profileExist->access()->dissociate();
                    $profileExist->access()->associate($profile['access']);
                    $profileExist->save();

                    $profileExist->update($attributes);
                });
                continue;
            }

            if ($accessService->findAccessById($profile['access'])) {
                DB::transaction(function () use ($user, $profile, $attributes, &$hasNewProfile, $profileService) {
                    $newProfile = $profileService->createProfile($attributes);
                    $newProfile->user()->associate($user);
                    $newProfile->access()->associate($profile['access']);
                    $newProfile->save();
                    $newProfile->refresh();

                    $hasNewProfile = true;
                });
            }
        }

        DB::transaction(function () use ($user, $userAttr) {
            $user->update($userAttr);
            $user->refresh();
        });

        if (!$user->profiles()->exists()) {
            return $this->getErrorMessage('profiles', 'Профиль с разрешенным доступом не найден. Напишите в поддержку: techbug@inpsycho.ru');
        }

        if ($hasNewProfile) {
            $mail = new WelcomeMail();
            MailSendJob::dispatch($user->email, $mail);
        }

        return true;
    }
}
