<?php

namespace App\Http\Controllers;

use App\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class SongController extends Controller
{
    /**
     * @var
     */
    protected $user;

    /**
     * SongController constructor.
     */
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Display a listing of songs.
     *
     * @return Response
     */
    public function index()
    {
        $songs = $this->user->songs()->get(['artist', 'title', 'cues'])->toArray();

        return $songs;
    }

    /**
     * Display a listing of songs that needs to be verified.
     *
     * @return Response
     */
    public function verify()
    {
        $songs = $this->user->songs()->where('is_accepted', '0')->get(['artist', 'title', 'cues', 'is_accepted'])->toArray();

        return $songs;
    }

    /**
     * Display song by id
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $song = $this->user->songs()->find($id);

        if (!$song) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, song with id ' . $id . ' cannot be found.'
            ], 400);
        }

        return $song;
    }

    /**
     * Display a listing of songs that were uploaded by a specific user.
     *
     * @param Song $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function userSongs(Song $user_id)
    {
        $song = $this->user->songs()->get($user_id);

        if (!$song) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, this user did not upload any songs or user does not exist.'
            ], 400);
        }
        else
        {
            return $song = $this->user->songs()->get(['artist', 'title', 'cues', 'is_accepted'])->toArray();
        }
    }

    /**
     * Store a newly created song.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'artist' => 'required',
            'title' => 'required',
            'cues' => 'required',
            'link' => 'required',
        ]);

        $song = new Song();
        $song->artist = $request->artist;
        $song->title = $request->title;
        $song->cues = $request->cues;
        $song->is_accepted = 0;
        $merge = $request->artist . ' ' . $request->title;
        $song->slug = Str::slug($merge, '-');

        if($contains = Str::contains($link = $request->link, 'youtube'))
        {
            $song->provider_id = '1';
            $video = $song->video_id = explode("?v=", $link);
            $song->video_id = $video[1];
        }
        else if($contains = Str::contains($link = $request->link, 'vimeo'))
        {
            $song->provider_id = '2';
            $video = $song->video_id = explode(".com/", $link);
            $song->video_id = $video[1];
        }
        else
        {
            return response()->json([
                'success' => false,
                'message' => 'Please give an URL from either Vimeo or Youtube.'
            ], 500);
        }

        if ($this->user->songs()->save($song))
            return response()->json([
                'success' => true,
                'song' => $song
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Sorry, song could not be added.'
            ], 500);
    }

    /**
     * Update the specified song.
     *
     * @param Request $request
     * @param Song $song
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Song $song)
    {
        if (!$song) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, song cannot be found.'
            ], 400);
        }

        $updated = $song->update($request->only(['title', 'cues', 'artist', 'is_accepted']));

        if ($updated) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, song could not be updated.'
            ], 500);
        }
    }

    /**
     * Remove the specified song.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $song = $this->user->songs()->find($id);

        if (!$song) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, song with id ' . $id . ' cannot be found.'
            ], 400);
        }

        if ($song->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Song could not be deleted.'
            ], 500);
        }
    }
}
