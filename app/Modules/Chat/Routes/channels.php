<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{receiver}.{sender}', function ($receiver, $sender) {
    return Auth::user() === $sender || Auth::user() === $receiver;
});
