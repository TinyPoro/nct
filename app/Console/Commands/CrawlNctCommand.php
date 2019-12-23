<?php

namespace App\Console\Commands;

use App\Models\Playlist;
use Illuminate\Console\Command;
use Nesk\Puphpeteer\Puppeteer;
use Nesk\Rialto\Data\JsFunction;

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
     * Puppeteer browser
     *
     * @var string
     */
    private $browser;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $puppeteer = new Puppeteer();
        $this->browser = $puppeteer->launch();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url = $this->argument('url');

        $page = $this->browser->newPage();
        $page->goto($url);

        $albumUrlSelector = config('crawl.nct.album_url_selector');

        $albumUrls = $page->evaluate(JsFunction::createWithBody("
            let albumUrls = [];
            
            let albums = document.querySelectorAll('" . $albumUrlSelector . "')
            
            albums.forEach((album) => {
                albumUrls.push(album.href);
            });
            
            return albumUrls;
        "));

        foreach ($albumUrls as $albumUrl) {
            $md5AlbumUrl = md5($albumUrl);

            try{
                $playlist = Playlist::create([
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
