<?php

namespace App\Console\Commands;

use App\Modules\User\Models\Profile;
use Illuminate\Console\Command;

class CreateProfileTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile:create-token {profile-id : ID профиля для создания токена}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создает новый токен для указанного профиля';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $profileId = $this->argument('profile-id');

        $profile = Profile::find($profileId);

        if (!$profile) {
            $this->error("Профиль с ID {$profileId} не найден");
            return Command::FAILURE;
        }

        // Проверяем, есть ли у профиля связанный пользователь
        if (!$profile->user) {
            $this->error("У профиля с ID {$profileId} нет связанного пользователя");
            return Command::FAILURE;
        }

        // Удаляем существующие токены профиля
        $profile->tokens()->delete();

        // Создаем новый токен
        $token = $profile->createToken($profile->user->email . '_token')->plainTextToken;

        $this->info("Токен успешно создан для профиля ID {$profileId}");
        $this->line("Email пользователя: {$profile->user->email}");
        $this->line("Токен: {$token}");

        return Command::SUCCESS;
    }
}
