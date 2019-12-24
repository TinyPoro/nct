<?php

namespace App\Http\Controllers;


use App\Exceptions\MediaDownloadUrlFailException;
use App\Models\Media;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class MediaController extends Controller
{
    private $client;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    public function download($mediaId)
    {
        try {
            $media = Media::findOrFail($mediaId);
            $downloadPath = storage_path($media->id);
            $downloadDestination = fopen($downloadPath, 'w');
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }

        try {
            $downloadableUrl = $media->url;

            $response = $this->client->get($downloadableUrl, [
                'sink' => $downloadDestination,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new MediaDownloadUrlFailException("Can not download media!");
            }

            return response()->download($downloadPath)->deleteFileAfterSend(true);

        } catch (GuzzleException $e) {
            $this->getNewDownloadableMediaUrl($media, $downloadDestination, $downloadPath);

        } catch (MediaDownloadUrlFailException $e) {
            $this->getNewDownloadableMediaUrl($media, $downloadDestination, $downloadPath);

        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    private function getNewDownloadableMediaUrl($media, $downloadDestination, $downloadPath)
    {
        try {
            $media->getNewDownloadableUrl();
            $media->touch();

            $downloadableUrl = $media->url;

            $response = $this->client->get($downloadableUrl, [
                'sink' => $downloadDestination,
            ]);

            if ($response->getStatusCode() !== 200) {
                return response()->json("Can not download media!");
            }

            return response()->download($downloadPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }
}
