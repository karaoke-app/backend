<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Song;
use App\Rating;

class RatingController extends Controller
{
    protected $user;
    protected $song;

    public function store(Request $request)
    {
        $this->validate($request, [
            'rate' => 'required',
        ]);

        $song = Song::find($request->id);

        $rating = new Rating();
        $rating->rate = $request->rate;
        $rating->user_id = auth()->user()->id;
        $rating->song_id = $song->id;

        if ($song->ratings()->save($rating))
            return response()->json([
                'success' => true,
                'task' => $rating
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Sorry, this song could not be rated.'
            ], 500);
    }
}
