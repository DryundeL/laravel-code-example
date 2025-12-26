<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\User\Models\Profile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearProfileCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-profiles {--user-id= : ID пользователя для очистки кэша} {--profile-id= : ID профиля для очистки кэша}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очищает кэш профилей и пользователей';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = $this->option('user-id');
        $profileId = $this->option('profile-id');

        if ($userId) {
            $this->clearUserCache($userId);
        } elseif ($profileId) {
            $this->clearProfileCache($profileId);
        } else {
            $this->clearAllProfileCache();
        }

        return Command::SUCCESS;
    }

    private function clearUserCache(int $userId): void
    {
        $user = User::find($userId);
        if (!$user) {
            $this->error("Пользователь с ID {$userId} не найден");
            return;
        }

        Cache::forget('users:' . $user->email . ':accesses');
        Cache::forget('user_' . $user->id . '_profiles');
        Cache::forget('user_' . $user->id . '_esb_profiles');

        $this->info("Кэш пользователя {$user->email} очищен");
    }

    private function clearProfileCache(int $profileId): void
    {
        $profile = Profile::find($profileId);
        if (!$profile) {
            $this->error("Профиль с ID {$profileId} не найден");
            return;
        }

        Cache::forget('profile_' . $profile->id . '_debt');
        Cache::forget('profile_' . $profile->id . '_unread_notifications_count');
        Cache::forget('user_' . $profile->user_id . '_profiles');
        Cache::forget('user_' . $profile->user_id . '_esb_profiles');

        $this->info("Кэш профиля ID {$profileId} очищен");
    }

    private function clearAllProfileCache(): void
    {
        $this->info("Очистка всего кэша профилей...");

        // Очищаем кэш ESB токена
        Cache::forget('esb_token');

        // Очищаем кэш профилей
        $profiles = Profile::all();
        foreach ($profiles as $profile) {
            Cache::forget('profile_' . $profile->id . '_debt');
            Cache::forget('profile_' . $profile->id . '_unread_notifications_count');
        }

        // Очищаем кэш пользователей
        $users = User::all();
        foreach ($users as $user) {
            Cache::forget('users:' . $user->email . ':accesses');
            Cache::forget('user_' . $user->id . '_profiles');
            Cache::forget('user_' . $user->id . '_esb_profiles');
        }

        $this->info("Весь кэш профилей очищен");
    }
}
