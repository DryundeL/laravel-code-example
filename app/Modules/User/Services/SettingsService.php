<?php

namespace App\Modules\User\Services;

use App\Models\User;
use App\Services\BaseService;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettingsService extends BaseService
{
    /**
     * @throws Exception
     */
    public function updateSettings(array $attributes): ?Authenticatable
    {
        $profile = Auth::user();
        $user = $profile?->user;

        return DB::transaction(function () use ($user, $attributes) {
            $user->update($attributes);
            $user->refresh();

            return $user;
        });
    }
}
