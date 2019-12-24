<?php

namespace App\Console\Commands;

use App\Main\NctPuPHPeteerCrawler;
use App\Models\Playlist;
use Illuminate\Console\Command;

class CrawlNctDetailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl_detail:nct';

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
        $page = $this->puPHPeteerCrawler->createNewPage();

        Playlist::where('status', Playlist::NOT_CRAWL_STATUS)->orderBy('id')->chunk(50, function($playlists) use ($page) {
            foreach ($playlists as $playlist) {
                try {
                    \DB::beginTransaction();

                    /** @var Playlist $playlist */
                    $url = $playlist->getUrl();

                    $page->goto($url);

                    $playlistName = $this->puPHPeteerCrawler->getPlaylistName($page);
                    $playlistArtist = "";
                    $playlistImage = "";

                    $mediaKeys = $this->puPHPeteerCrawler->getNctAlbumSongsKeys($page);

                    foreach ($mediaKeys as $mediaKey) {
                        dd($mediaKey);
                    }


                    dd("done");
                    $playlist->status = Playlist::CRAWLED_STATUS;
                    $playlist->save();
                    \DB::commit();
                } catch (\Exception $e) {
                    \Log::error("Error at crawl_detail:nct command: " . $e->getMessage());

                    \DB::rollback();

                    $playlist->status = Playlist::CRAWLED_ERROR_STATUS;
                    $playlist->save();
                }
            }
        });
    }


}
