<?php

namespace App\Http\Controllers;

use App\Playlist;
use App\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{
    /**
     * @var
     */
    protected $song;

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

        $songs = Song::find([2,3]);
        $playlist->songs()->attach($songs);

        return response()->json([
            'success' => true,
            'task' => $playlist
        ]);
    }

    /**
     * Remove the specified playlist.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $playlist = Playlist::find($id);

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
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function remove(Playlist $playlist)
    {
        $song = Song::find(2);

        if (!$playlist) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, playlist cannot be found.'
            ], 400);
        }

        if ($playlist->songs()->detach($song)) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Song from given playlist could not be deleted.'
            ], 500);
        }
    }
}
