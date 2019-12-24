<?php
/**
 * Created by PhpStorm.
 * User: TinyPoro
 * Date: 12/23/19
 * Time: 11:40 PM
 */

namespace App\Main;


use App\Models\Media;
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

    public function getMediaPropertiesByKey($key)
    {
        $mediaProperties = [];

        $page = $this->createNewPage();

        $mediaUrl = Media::getNctMediaUrlByKey($key);
        $page->goto($mediaUrl);

        $mediaTypeSelector = config('crawl.nct.media.type_selector');
        $mediaProperties["type"] = $this->getElementAttribute($page, $mediaTypeSelector, "getAttribute('itemtype')");

        $mediaTitleSelector = config('crawl.nct.media.title_selector');
        $mediaProperties["title"] = $this->getElementAttribute($page, $mediaTitleSelector, "innerText");

        $mediaArtistSelector = config('crawl.nct.media.artist_selector');
        $mediaProperties["artists"] = $this->getElementAttribute($page, $mediaArtistSelector, "innerText");

        $mediaImageSelector = config('crawl.nct.media.image_selector');
        $mediaProperties["image"] = $this->getElementAttribute($page, $mediaImageSelector, "href");

        $mediaProperties["url"] = Media::getNctDownloadableLinkFromKey($key);

        return $mediaProperties;
    }



    public function getNctAlbumMediasKeys($page)
    {
        $mediaKeySelector = config('crawl.nct.media.key_selector');

        return $this->getElementsAttribute($page, $mediaKeySelector, "getAttribute('key')");


    }
}