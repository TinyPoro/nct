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
use Nesk\Rialto\Data\JsFunction;

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

    public function getMediaPropertiesByKey($type, $key)
    {
        $mediaProperties = [];

        $page = $this->createNewPage();

        $mediaWebUrl = Media::getNctMediaWebUrlByKey($type, $key);
        $page->goto($mediaWebUrl);

        $mediaTitleSelector = config('crawl.nct.media.title_selector');
        $mediaProperties["title"] = $this->getElementAttribute($page, $mediaTitleSelector, "innerText");

        $mediaArtistSelector = config('crawl.nct.media.artist_selector');
        $mediaProperties["artists"] = $this->getElementAttribute($page, $mediaArtistSelector, "innerText");

        $mediaImageSelector = config('crawl.nct.media.image_selector');
        $mediaProperties["image"] = $this->getElementAttribute($page, $mediaImageSelector, "href");

        $mediaProperties["url"] = Media::getNctDownloadableLinkFromKey($type, $key);

        return $mediaProperties;
    }



    public function getNctAlbumMediasKeysAndUrls($page)
    {
        $mediaKeySelector = config('crawl.nct.media.key_selector');
        $urlKeySelector = config('crawl.nct.media.url_selector');

        return $page->evaluate(JsFunction::createWithBody("
            let results = [];
            
            let elements = document.querySelectorAll('" . $mediaKeySelector . "')
            
            elements.forEach((element) => {
                let url_element = document.querySelector('" . $urlKeySelector . "')
                results.push({
                    'key': element.getAttribute('key'),
                    'url': (null === url_element) ? '' : url_element.getAttribute('href')
                });
            });
            
            return results;
        "));
    }
}