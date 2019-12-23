<?php

namespace App\Console\Commands;

use App\Main\PuPHPeteerCrawler;
use App\Models\Playlist;
use Illuminate\Console\Command;

class CrawlNctCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:nct
    {url : Select url to crawl}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * PuPHPeteerCrawler
     *
     * @var PuPHPeteerCrawler
     */
    private $puPHPeteerCrawler;

    /**
     * Create a new command instance.
     *
     * @param PuPHPeteerCrawler $puPHPeteerCrawler
     * @return void
     */
    public function __construct(PuPHPeteerCrawler $puPHPeteerCrawler)
    {
        parent::__construct();

        $this->puPHPeteerCrawler = $puPHPeteerCrawler;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url = $this->argument('url');

        $page = $this->puPHPeteerCrawler->createNewPage();

        $this->puPHPeteerCrawler->visit($page, $url);

        $albumUrlSelector = config('crawl.nct.album_url_selector');

        $albumUrls = $this->puPHPeteerCrawler->getElementsAttribute($page, $albumUrlSelector, "href");

        foreach ($albumUrls as $albumUrl) {
            $md5AlbumUrl = md5($albumUrl);

            try{
                Playlist::create([
                    'url' => $albumUrl,
                    'md5_url' => $md5AlbumUrl,
                ]);
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }

        $page->close();
    }
}
