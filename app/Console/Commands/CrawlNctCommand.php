<?php

namespace App\Console\Commands;

use App\Main\NctPuPHPeteerCrawler;
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
     * @var NctPuPHPeteerCrawler
     */
    private $puPHPeteerCrawler;

    /**
     * Create a new command instance.
     *
     * @param NctPuPHPeteerCrawler $puPHPeteerCrawler
     * @return void
     */
    public function __construct(NctPuPHPeteerCrawler $puPHPeteerCrawler)
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
        $page->goto($url);

        $playlistUrls = $this->puPHPeteerCrawler->getPlaylistUrls($page);

        foreach ($playlistUrls as $playlistUrl) {
            $md5PlaylistUrl = md5($playlistUrl);

            try{
                Playlist::create([
                    'url' => $playlistUrl,
                    'md5_url' => $md5PlaylistUrl,
                ]);
            } catch (\Exception $e) {
                \Log::error("Error at crawl:nct command: " . $e->getMessage());
            }
        }

        $page->close();
    }
}
