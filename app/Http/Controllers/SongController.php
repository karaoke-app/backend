<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSong;
use App\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SongController extends Controller
{
    /**
     * Display a listing of songs.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $songs = Song::query();

        if ($request->has('sort') && $request->get('sort') === 'date_desc') {
        $songs->orderBy('created_at', 'desc');
        }

        if ($request->has('query')) {
            $query = $request->get('query');
            $songs->where('artist', 'like', '%' . $query . '%')
                ->orWhere('title', 'like', '%' . $query . '%');
        }

        if ($request->has('category')) {
            $category = $request->get('category');
            $songs->whereHas('categories', function ($q) use ($category) {
                $q->where('category_id', $category);
            });
        }
        return $songs->paginate(6, ['id', 'slug', 'artist', 'title'])->appends($request->input());
    }

    /**
     * Display a listing of songs that needs to be verified.
     *
     * @return Response
     */
    public function verify()
    {
        $songs = Song::where('is_accepted', '0')->get(['artist', 'title', 'cues', 'is_accepted'])->toArray();

        return $songs;
    }

    /**
     * Display song by id
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $song = Song::with('user:id,name,avatar')->find($id);

        if (!$song) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, song with id ' . $id . ' cannot be found.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'song' => $song,
        ]);
    }

    /**
     * Display a listing of songs that were uploaded by a specific user.
     *
     * @param Song $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function userSongs($user_id)
    {
        $song = Song::where('user_id', $user_id)->first();

        if (!$song) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, this user did not upload any songs or user does not exist.',
            ], 400);
        } else {
            return $song = Song::where('user_id', $user_id)->get(['id', 'slug', 'artist', 'title', 'cues'])->toArray();
        }
    }

    /**
     * Store a newly created song.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(StoreSong $request)
    {
        $song = new Song();
        $song->artist = $request->artist;
        $song->title = $request->title;
        $song->video_id = $request->video_id;
        $song->cues = $request->cues;
        $song->provider_id = $request->provider_id;
        $merge = $request->artist . ' ' . $request->title;
        $song->slug = Str::slug($merge, '-');

        if (auth()->user()->songs()->save($song)) {
            $song->categories()->attach($request->categories);
            return response()->json([
                'success' => true,
                'song' => $song
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, song could not be added.',
            ], 500);
        }
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
                'message' => 'Sorry, song cannot be found.',
            ], 400);
        }

        $updated = $song->update($request->only(['title', 'cues', 'artist', 'is_accepted']));

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Song has been updated',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, song could not be updated.',
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
        $song = Song::find($id);

        if (!$song) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, song with id ' . $id . ' cannot be found.',
            ], 400);
        }

        if ($song->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Song was successfully removed',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Song could not be deleted.',
            ], 500);
        }
    }
}
