<?php

use App\Models\Esp;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('esp.{espId}', function ($user, $espId) {

    // Check if the authenticated user owns the ESP with the provided ID
    return $user->id === Esp::findOrFail($espId)->user_id;
});
