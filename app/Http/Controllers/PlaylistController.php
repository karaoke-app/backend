<?php

namespace App\Http\Controllers;

use App\Playlist;
use App\Song;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{
    /**
     * Create a new playlist.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $playlist = new Playlist();
        $playlist->name = $request->name;
        $playlist->is_private = 0;
        $playlist->user_id = Auth::user()->id;

        $playlist->save();

        return response()->json([
            'success' => true,
            'playlist' => $playlist
        ],201);
    }

    /**
     * Add song to a playlist.
     *
     * @param Playlist $playlist_id
     * @param Song $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function add($playlist_id, $id)
    {
        $playlist = Playlist::find($playlist_id);

        $this->authorize('add', $playlist);

        if (!$playlist) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, playlist with given id does not exist.'
            ], 400);
        }

        $song = Song::where('id', $id)->get();

        if (!$song) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, song with given id does not exist.'
            ], 400);
        }

        $playlist->songs()->attach($song);

        return response()->json([
            'success' => true,
            'message' => 'Song was added to the playlist.'
        ]);
    }

    /**
     * Remove the specified playlist.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $playlist = Playlist::find($id);

        $this->authorize('destroy', $playlist);

        if (!$playlist) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, playlist with id ' . $id . ' cannot be found.'
            ], 400);
        }

        if ($playlist->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Playlist could not be deleted.'
            ], 500);
        }
    }

    /**
     * Remove the specified song from specified playlist.
     *
     * @param Playlist $playlist
     * @param Song $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function remove(Playlist $playlist, $id)
    {
        $song = Song::find($id);

        $this->authorize('remove', $playlist);

        if (!$playlist) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, playlist cannot be found.'
            ], 400);
        }

        if ($playlist->songs()->detach($song)) {
            return response()->json([
                'success' => true,
                'message' => 'Song from given playlist was successfully removed.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Song from given playlist could not be deleted.'
            ], 500);
        }
    }

    /**
     * Display a listing of playlists.
     *
     * @return Response
     */
    public function index()
    {
        $playlists = auth()->user()->playlists()->with(['songs' => function ($q) {
            $q->select('song_id', 'artist', 'title');
        }])->get(['id', 'name']);

        return $playlists->toArray();
    }

    /**
     * Display playlists from specified user.
     *
     * @param User $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function userPlaylist($id)
    {
        $playlists = Playlist::where('user_id', $id)
            ->with('songs:song_id,title,artist');

        $user = auth()->user();

        if (!($user && $user->id === (int) $id)) {
            $playlists->where('is_private', 0);
        }

        $playlists = $playlists->get()->toArray();

        if (!$playlists) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user did not create any playlists.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'playlist' => $playlists,
        ]);
    }

    /**
     * Display songs from specified playlist
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $playlist = Playlist::find($id);

        if (!$playlist) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, playlist with id ' . $id . ' cannot be found.'
            ], 400);
        }

        $currentPlaylist = $playlist->songs()->get(['song_id', 'title', 'artist'])->toArray();

        return response()->json([
            'success' => true,
            'playlist' => $currentPlaylist
        ]);
    }
}
