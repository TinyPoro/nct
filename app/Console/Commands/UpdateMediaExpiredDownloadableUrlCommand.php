<?php

namespace App\Console\Commands;

use App\Main\NctPuPHPeteerCrawler;
use App\Models\Media;
use App\Models\Playlist;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateMediaExpiredDownloadableUrlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:expired_url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cứ delay 30 phút chạy kiểm tra 1 lần, 
    nếu media nào có thời gian expired <= now + 60 phút thì lấy link mới.
    Mục tiêu là tránh việc người dùng phải chờ hệ thống lấy link mới khi tải media hết hạn.
    Hiện tại expired time của NCT đang đặt là 1 ngày, nên mốc 30 phút thì OK.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }



    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $checkTime = Carbon::now()->addMinutes(60);

        Media::whereDate('expired_url', '<=', $checkTime)->orderBy('id')->chunk(50, function($medias) {
            foreach ($medias as $media) {
                $media->getNewDownloadableUrl();
            }
        });
    }
}
