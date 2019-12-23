<?php
/**
 * Created by PhpStorm.
 * User: TinyPoro
 * Date: 12/23/19
 * Time: 11:40 PM
 */

namespace App\Main;


use Nesk\Puphpeteer\Puppeteer;
use Nesk\Rialto\Data\JsFunction;

class PuPHPeteerCrawler
{
    /**
     * Puppeteer browser
     *
     * @var string
     */
    private $browser;

    public function __construct()
    {
        $puppeteer = new Puppeteer();
        $this->browser = $puppeteer->launch();
    }

    public function createNewPage()
    {
        return $this->browser->newPage();
    }

    public function visit($page, $url)
    {
        $page->goto($url);
    }

    public function getElementsAttribute($page, $selector, $attribute)
    {
        return $page->evaluate(JsFunction::createWithBody("
            let results = [];
            
            let elements = document.querySelectorAll('" . $selector . "')
            
            elements.forEach((element) => {
                results.push(element." . $attribute . ");
            });
            
            return results;
        "));
    }
}