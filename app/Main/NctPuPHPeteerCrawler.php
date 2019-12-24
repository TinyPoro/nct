<?php
/**
 * Created by PhpStorm.
 * User: TinyPoro
 * Date: 12/23/19
 * Time: 11:40 PM
 */

namespace App\Main;


use GuzzleHttp\Client;

class NctPuPHPeteerCrawler extends PuPHPeteerCrawler
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getPlaylistUrls($page)
    {
        $playlistUrlSelector = config('crawl.nct.playlist.url_selector');

        return $this->getElementsAttribute($page, $playlistUrlSelector, "href");
    }

    public function getPlaylistName($page)
    {
        $playlistNameSelector = config('crawl.nct.playlist.name_selector');

        return $this->getElementAttribute($page, $playlistNameSelector, "innerText");
    }

    /**
     * Get Nct Download media url. Only support 128kbps for this version.
     *
     * @var string
     * @var integer
     *
     * @return string
     */
    public function getNctDownloadMediaUrlByKey($key, $quality = 128)
    {
        return "https://www.nhaccuatui.com/download/song/$key" . "_" . $quality;
    }

    public function getNctMediaUrlByKey($key)
    {
        return "https://www.nhaccuatui.com/bai-hat/a.$key.html";
    }

    public function getMediaPropertiesByKey($key)
    {
        $mediaProperties = [];

        $page = $this->createNewPage();

        $mediaUrl = $this->getNctMediaUrlByKey($key);
        $page->goto($mediaUrl);

        $mediaTypeSelector = config('crawl.nct.media.type_selector');
        $mediaProperties["type"] = $this->getElementAttribute($page, $mediaTypeSelector, "getAttribute('itemtype')");

        $mediaTitleSelector = config('crawl.nct.media.title_selector');
        $mediaProperties["title"] = $this->getElementAttribute($page, $mediaTitleSelector, "innerText");

        $mediaArtistSelector = config('crawl.nct.media.artist_selector');
        $mediaProperties["artists"] = $this->getElementAttribute($page, $mediaArtistSelector, "innerText");

        $mediaImageSelector = config('crawl.nct.media.image_selector');
        $mediaProperties["image"] = $this->getElementAttribute($page, $mediaImageSelector, "href");

        $mediaProperties["url"] = $this->getNctDownloadableLinkFromKey($key);

        return $mediaProperties;
    }

    public function getNctDownloadableLinkFromKey($key)
    {
        $client = new Client();

        $downloadMediaUrl = $this->getNctDownloadMediaUrlByKey($key);
        $mediaUrl = $this->getNctMediaUrlByKey($key);

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

    public function getNctAlbumMediasKeys($page)
    {
        $mediaKeySelector = config('crawl.nct.media.key_selector');

        return $this->getElementsAttribute($page, $mediaKeySelector, "getAttribute('key')");


    }
}