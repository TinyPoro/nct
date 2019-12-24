<?php

return [
    "nct" => [
        "playlist" => [
            "url_selector" => ".list_album ul > li > .box-left-album > a",
            "name_selector" => ".name_title"
        ],
        "media" => [
            "key_selector" => ".list_song_in_album li",
            "url_selector" => ".item_content a.name_song",
            "title_selector" => ".name_title h1",
            "artist_selector" => ".name-singer",
            "image_selector" => "link[rel=\"image_src\"]",
        ],
    ]
];