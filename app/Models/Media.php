<?php

namespace App\Models;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{

    const VIDEO_TYPE = 0;
    const AUDIO_TYPE = 1;
    const UNDEFINED_TYPE = -1;

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

    /**
     * Get Nct Download media url. Only support 128kbps(audio) and 480kbps(video) for this version.
     *
     * @var string
     * @var integer
     *
     * @return string
     */
    public static function getNctDownloadMediaUrlByKey($type, $key)
    {
        if ($type === self::AUDIO_TYPE) {
            return "https://www.nhaccuatui.com/download/song/$key" . "_128";
        } elseif ($type === self::VIDEO_TYPE) {
            return "https://www.nhaccuatui.com/download/video/$key" . "_480";
        } else {
            return "";
        }

    }

    public static function getNctMediaWebUrlByKey($type, $key)
    {
        if ($type === self::AUDIO_TYPE) {
            return "https://www.nhaccuatui.com/bai-hat/a.$key.html";
        } elseif ($type === self::VIDEO_TYPE) {
            return "https://www.nhaccuatui.com/video/a.$key.html";
        } else {
            return "";
        }
    }

    public static function getNctMediaTypeByUrl($url)
    {
        if (preg_match("/nhaccuatui.com/bai-hat", $url)) {
            return self::AUDIO_TYPE;
        } elseif (preg_match("/nhaccuatui.com/video", $url)) {
            return self::VIDEO_TYPE;
        } else {
            return self::UNDEFINED_TYPE;
        }
    }

    public static function getNctDownloadableLinkFromKey($type, $key)
    {
        $client = new Client();

        $downloadMediaUrl = self::getNctDownloadMediaUrlByKey($type, $key);
        $mediaWebUrl = self::getNctMediaWebUrlByKey($type, $key);

        $res = $client->request(
            'GET',
            $downloadMediaUrl,
            [
                'headers' => [
                    'referer' => $mediaWebUrl
                ]
            ]
        );

        $response = json_decode($res->getBody(), true);

        $error_code = array_get($response, "error_code", null);

        if ($error_code === 0) {
            $downloadableUrl = array_get($response, "data.stream_url", null);

            if (!$downloadableUrl) {
                throw new \Exception("Can not download this media!");
            } else {
                return $downloadableUrl;
            }
        } else {
            throw new \Exception("Can not download this media!");
        }
    }

    public function getNewDownloadableUrl(){
        try {
            $this->url = self::getNctDownloadableLinkFromKey($this->type, $this->key);
        } catch (\Exception $e) {
            self::destroy($this->id);
        }
    }
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

    public function getTypeTextAttribute(){
        if ($this->type === self::VIDEO_TYPE) {
            return "Video";
        } elseif ($this->type === self::AUDIO_TYPE) {
            return "Audio";
        } else {
            return "N/A";
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
