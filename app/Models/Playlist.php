<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    const NOT_CRAWL_STATUS = 0;
    const CRAWLED_STATUS = 1;
    const CRAWLED_ERROR_STATUS = -1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'md5_url',
        'name',
        'artist',
        'image',
        'status'
    ];
}