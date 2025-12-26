<?php

use App\Modules\User\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('notifications.{profile}', static function (Profile $profile) {
    return Auth::user()->id === $profile->id;
});
