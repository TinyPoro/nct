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
$router->get('media/{media_id}/download', [
    "as" => "media.download",
    "uses" => "MediaController@download"
]);

$router->get('playlist/{playlist_id}/medias', [
    "as" => "playlist.medias",
    "uses" => "PlaylistController@showMedias"
]);
