<?php
/**
 * Created by PhpStorm.
 * User: TinyPoro
 * Date: 12/23/19
 * Time: 11:40 PM
 */

namespace App\Main;


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

    public function getMediaPropertyByKey($key)
    {

    }

    public function getNctAlbumSongsKeys($page)
    {
        $songKeySelector = config('crawl.nct.song.key_selector');

        return $page->evaluate(JsFunction::createWithBody("
            let results = [];
            
            let elements = document.querySelectorAll('" . $songKeySelector . "')
            
            let getText = (element, selector) => {
                let textElement = element.querySelector(selector)
                
                return (null === textElement) ? '' : textElement.innerText
            }
            
            elements.forEach((element) => {
                results.push(element.getAttribute('key'));
            });
            
            return results;
        "));
    }
}