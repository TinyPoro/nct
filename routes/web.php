<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('playlist', "PlaylistController@index");

$router->get('media/{mediaId}/download', [
    "as" => "media.download",
    "uses" => "MediaController@download"
]);

$router->get('playlist/{playlistId}/medias', [
    "as" => "playlist.medias",
    "uses" => "PlaylistController@showMedias"
]);

$router->get('playlist/medias/json', [
    "as" => "playlist.medias.json",
    "uses" => "PlaylistController@getMediasJson"
]);

$router->get('playlist/crawl', "PlaylistController@crawlNct");
