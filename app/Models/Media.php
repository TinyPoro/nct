<?php

namespace App\Models;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{

    const VIDEO_TYPE = 0;
    const AUDIO_TYPE = 1;

    public static $mediaTypeCodeMapping = [
        "http://schema.org/Unknown" => self::VIDEO_TYPE,
        "http://schema.org/MusicRecording" => self::AUDIO_TYPE
    ];


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

    public static function getMediaTypeCode($mediaTypeString)
    {
        return array_get(self::$mediaTypeCodeMapping, $mediaTypeString, self::VIDEO_TYPE);
    }

    /**
     * Get Nct Download media url. Only support 128kbps for this version.
     *
     * @var string
     * @var integer
     *
     * @return string
     */
    public static function getNctDownloadMediaUrlByKey($key, $quality = 128)
    {
        return "https://www.nhaccuatui.com/download/song/$key" . "_" . $quality;
    }

    public static function getNctMediaUrlByKey($key)
    {
        return "https://www.nhaccuatui.com/bai-hat/a.$key.html";
    }

    public static function getNctDownloadableLinkFromKey($key)
    {
        $client = new Client();

        $downloadMediaUrl = self::getNctDownloadMediaUrlByKey($key);
        $mediaUrl = self::getNctMediaUrlByKey($key);

        $res = $client->request(
            'GET',
            $downloadMediaUrl,
            [
                'headers' => [
                    'referer' => $mediaUrl
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
            $this->url = self::getNctDownloadableLinkFromKey($this->key);
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
        return ($this->type === self::VIDEO_TYPE) ? "Video" : "Audio";
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
