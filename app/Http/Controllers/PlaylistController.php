<?php

namespace App\Http\Controllers;

use App\Models\Playlist;

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

    public function showMedias($playlist_id)
    {
        $playlists = Playlist::findOrFail($playlist_id);

        $medias = $playlists->medias()->paginate(15);

        return view('playlist.medias', [
            'medias' => $medias
        ]);
    }
}
