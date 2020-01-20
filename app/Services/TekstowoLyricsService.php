<?php

namespace App\Services;

use Goutte\Client;

class TekstowoLyricsService
{
    public function import($artist, $title)
    {
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.tekstowo.pl/wyszukaj.html?search-artist=' . $artist . '&search-title=' . $title);

        $crawler = $client->request('GET',
            $crawler->filter('.content > .box-przeboje > a')
                ->first()
                ->extract(['href'])[0]);

        if($crawler->filter('.song-text')->count() <= 0)
            return response()->json([
                'success' => false,
                'lyrics' => 'Lyrics for song with given artist and title could not be found.'
            ]);

        $songText = $crawler->filter('.song-text')->html();

        $songText = str_replace([$crawler->filter('.song-text > h2')->first()->text(), 'Poznaj historię zmian tego tekstu'], '<br>', $songText);
        $songText = trim(str_replace('<br>', ' ', strip_tags($songText, '<br>')));

        return $songText;
    }
}
