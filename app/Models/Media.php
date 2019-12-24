<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{

    const VIDEO_TYPE = 0;
    const AUDIO_TYPE = 1;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'type',
        'title',
        'artists',
        'url',
        'image',
    ];

    protected $table = "medias";

    /*
   |--------------------------------------------------------------------------
   | FUNCTIONS
   |--------------------------------------------------------------------------
   */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function playlists(){
        return $this->belongsToMany(Playlist::class, 'media_playlist', 'media_id', 'playlist_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
