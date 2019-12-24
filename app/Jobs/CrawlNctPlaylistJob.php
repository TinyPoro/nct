<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CrawlNctPlaylistJob extends Job
{
    private $url;

    public function __construct($url)
    {
        $this->url = $url;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            Artisan::call( 'crawl:nct', ['url' => $this->url]);
        }catch (\Exception $e){
            Log::error($e->getMessage());

        }
    }
}
