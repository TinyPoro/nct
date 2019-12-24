<?php

namespace App\Http\Controllers;

use App\Jobs\CrawlNctPlaylistJob;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\View;

class PlaylistController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $playlists = Playlist::paginate(15);

        return view('playlist.index', [
            'playlists' => $playlists
        ]);
    }

    public function showMedias($playlistId)
    {
        $playlists = Playlist::findOrFail($playlistId);

        $medias = $playlists->medias()->paginate(15);

        return view('playlist.medias', [
            'playlistId' => $playlistId,
            'medias' => $medias
        ]);
    }

    public function getMediasJson(Request $request)
    {
        $playlistId = $request->get('playlistId');
        $search = $request->get('search');

        $playlists = Playlist::findOrFail($playlistId);

        $mediaBuilder = $playlists->medias();

        if ($search) {
            $mediaBuilder = $mediaBuilder->where('title', 'like', "%$search%")
                ->orWhere('artists', 'like', "%$search%");
        }

        $medias = $mediaBuilder->paginate(15);

        return response()->json(View::make('playlist.media_table', [
            'playlistId' => $playlistId,
            'medias' => $medias
            ])->render());
    }

    public function crawlNct(Request $request)
    {
        try {
            $url = $request->get('url');

            Queue::push(new CrawlNctPlaylistJob($url));

            return response()->json('Success');
        } catch (\Exception $e) {
            return response()->json('Fail');
        }
    }
}
