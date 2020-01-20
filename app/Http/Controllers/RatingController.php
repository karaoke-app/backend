<?php

namespace App\Http\Controllers;

use App\Rating;
use App\Song;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function show($id)
    {
        $user = auth()->user();
        $rates = Rating::where('song_id', $id);
        $data = [
            'avg' => (float) $rates->avg('rate'),
            'count' => $rates->count('rate'),
            'voted' => false,
        ];
        if ($user) {
            $data['voted'] = $rates->where('user_id', $user->id)->count() > 0;
        }
        return response()->json([
            'success' => true,
            'ratings' => $data,
        ]);
    }

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

        if ($song->ratings()->save($rating)) {
            return response()->json([
                'success' => true,
                'task' => $rating,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, this song could not be rated.',
            ], 500);
        }
    }
}
