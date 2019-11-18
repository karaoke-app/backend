<?php

namespace App\Http\Controllers;

use App\Song;
use Illuminate\Http\Request;
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
        $songs = $this->user->songs()->get(['title', 'songText', 'category'])->toArray();

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
     * Store a newly created song.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'songText' => 'required',
            'category' => 'required',
        ]);

        $song = new Song();
        $song->title = $request->title;
        $song->songText = $request->songText;
        $song->category = $request->category;

        if ($this->user->songs()->save($song))
            return response()->json([
                'success' => true,
                'task' => $song
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
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $song = $this->user->songs()->find($id);

        if (!$song) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, song with id ' . $id . ' cannot be found.'
            ], 400);
        }

        $updated = $song->fill($request->all())->save();

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
