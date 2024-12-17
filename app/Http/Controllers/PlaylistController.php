<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Google_Client;
use Google_Service_YouTube;
use Illuminate\Http\JsonResponse;

class PlaylistController extends Controller
{
    public function getPlaylistVideos(): JsonResponse
    {
        $playlistId = config('services.youtube.playlist_id');
        $apiKey = config('services.youtube.api_key');

        $client = new Google_Client();
        $client->setDeveloperKey($apiKey);

        $youtube = new Google_Service_YouTube($client);

        try {
            $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet,contentDetails', [
                'playlistId' => $playlistId,
                'maxResults' => 50
            ]);

            $videos = [];
            foreach ($playlistItemsResponse->getItems() as $playlistItem) {
                $videoId = $playlistItem->getContentDetails()->getVideoId();

                $videoResponse = $youtube->videos->listVideos('snippet,contentDetails', [
                    'id' => $videoId
                ]);

                $video = $videoResponse->getItems()[0];
                $videos[] = [
                    'id' => $videoId,
                    'title' => $video->getSnippet()->getTitle(),
                    'description' => $video->getSnippet()->getDescription() ?? '',
                    'thumbnail' => $video->getSnippet()->getThumbnails()->getMedium()->getUrl(),
                    'duration' => $video->getContentDetails()->getDuration()
                ];
            }

            return response()->json($videos);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage() . "\n", $e->getTrace()], 500);
        }
    }
}
