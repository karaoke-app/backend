<?php

namespace App\Http\Controllers;

use App\Song;
use Illuminate\Http\JsonResponse;
use App\Services\TekstowoLyricsService;
use Illuminate\Http\Request;

class LyricsController extends Controller
{
    /**
     * @var TekstowoLyricsService
     */

    private $lyricsService;

    public function __construct(TekstowoLyricsService $lyrics)
    {
        $this->lyricsService = $lyrics;
    }

    /**
     * Import text for a specific song from website.
     *
     * @param Request $request
     * @return JsonResponse|string|string[]
     */
    public function import(Request $request)
    {
        return $this->lyricsService->import(
            $request->artist,
            $request->title
        );
    }
}
