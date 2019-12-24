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
}
