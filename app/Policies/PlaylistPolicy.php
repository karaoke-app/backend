<?php

namespace App\Policies;

use App\Playlist;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlaylistPolicy
{
    use HandlesAuthorization;

    /**
     * @param  \App\User  $user
     * @param  \App\Playlist  $playlist
     * @return bool
     */
    public function add(User $user, Playlist $playlist)
    {
        return $user->id === $playlist->user_id;
    }

    /**
     * @param  \App\User  $user
     * @param  \App\Playlist  $playlist
     * @return bool
     */
    public function destroy(User $user, Playlist $playlist)
    {
        return $user->id === $playlist->user_id;
    }

    /**
     * @param  \App\User  $user
     * @param  \App\Playlist  $playlist
     * @return bool
     */
    public function remove(User $user, Playlist $playlist)
    {
        return $user->id === $playlist->user_id;
    }
}
