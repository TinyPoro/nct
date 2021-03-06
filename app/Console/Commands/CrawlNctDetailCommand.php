<?php

namespace App\Console\Commands;

use App\Main\NctPuPHPeteerCrawler;
use App\Models\Media;
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
                    $playlistArtist = [];
                    $playlistImage = "";

                    $mediaKeysAndUrls = $this->puPHPeteerCrawler->getNctAlbumMediasKeysAndUrls($page);

                    $newMediaItems = [];

                    foreach ($mediaKeysAndUrls as $mediaKeyAndUrl) {
                        try {
                            $mediaKey = $mediaKeyAndUrl['key'];
                            $mediaWebUrl = $mediaKeyAndUrl['url'];

                            if (!$mediaKey or !$mediaWebUrl) {
                                continue;
                            }
                            $mediaType = Media::getNctMediaTypeByUrl($mediaWebUrl);

                            $mediaProperties = $this->puPHPeteerCrawler->getMediaPropertiesByKey($mediaType, $mediaKey);

                            $mediaTitle = $mediaProperties["title"];
                            $mediaArtists = explode(",", $mediaProperties["artists"]);
                            $mediaArtists = array_map('trim', $mediaArtists);
                            $mediaImage = $mediaProperties["image"];
                            $mediaUrl = $mediaProperties["url"];
                            $mediaExpiredUrlDatetime = Media::getExpiredDatetimeFromMediaDownloadableUrl($mediaUrl);

                            $media = Media::create([
                                'key' => $mediaKey,
                                'type' => $mediaType,
                                'title' => $mediaTitle,
                                'artists' => implode(", ", $mediaArtists),
                                'url' => $mediaUrl,
                                'image' => $mediaImage,
                                'expired_url' => $mediaExpiredUrlDatetime,
                            ]);

                            $newMediaItems[] = $media->id;
                        } catch (\Exception $e) {
                            \Log::error("Error at crawl_detail:nct - create media item: " . $e->getMessage());

                            continue;
                        }

                        if (!$playlistImage) {
                            $playlistImage = $mediaImage;
                        }

                        $playlistArtist = $playlistArtist + $mediaArtists;
                    }

                    if(count($newMediaItems) === 0) throw new \Exception("Can not crawl any media!");

                    $playlist->medias()->syncWithoutDetaching($newMediaItems);

                    $playlistArtist = array_unique($playlistArtist);

                    $playlist->name = $playlistName;
                    $playlist->artist = implode(", ", $playlistArtist);
                    $playlist->image = $playlistImage;
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
