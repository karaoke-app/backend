<?php

namespace App\Http\Controllers;

use App\Song;
use Illuminate\Http\JsonResponse;
use App\Services\TekstowoLyricsService;

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
     * @param Song $artist
     * @param Song $title
     * @return JsonResponse|string|string[]
     */
    public function import($artist, $title)
    {
        return $this->lyricsService->import($artist, $title);
    }
}
