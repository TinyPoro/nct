# NhacCuaTui Crawl by Lumen PHP Framework

**Author:** ngophuongtuan@gmail.com 

## Technology

- Lumen
- Mysql
- Nginx
- Puppeteer

## Setup

1. Check volumns path in `docker-compose.yml`, but be sure that volumns path must be shared with docker.
    ```$xslt
    #  The Application
      app:
        container_name: crawl_nct
        build:
          context: ./
          dockerfile: development/app.dockerfile
        volumes:
          - ./storage:/var/www/storage  --> Changeit
        env_file: '.env.prod'
        environment:
          - "DB_HOST=database"
          - "REDIS_HOST=cache"
          
          
     # The Web Server
      web:
        container_name: nginx_server
        build:
          context: ./
          dockerfile: development/web.dockerfile
        volumes:
          - ./storage/logs/:/var/log/nginx  --> Changeit
        ports:
          - 8990:80
    ```

2. Check ports in `docker-compose.yml`, be sure that port number in the left side of the colon has not been used. Default port for `nginx web` is 8990
and default port for `database` is 8991.

    ```$xslt
    # The Web Server
      web:
        container_name: nginx_server
        build:
          context: ./
          dockerfile: development/web.dockerfile
        volumes:
          - ./storage/logs/:/var/log/nginx
        command: nginx -g "daemon off;"
        ports:
          - 8990:80
    
      # The Database
      database:
        container_name: mysql_database
        image: mysql:5.7
        volumes:
          - dbdata:/var/lib/mysql
        environment:
          - "MYSQL_DATABASE=nct"
          - "MYSQL_USER=phpmyadmin"
          - "MYSQL_PASSWORD=phpmyadmin"
          - "MYSQL_ROOT_PASSWORD=root."
        ports:
          - 8991:3306
    ```

3. Update mysql database connection in `docker-compose.yml` and `.env.prod`, but be sure 
that `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD` in `docker-compose.yml` must equal to
`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` in `.env.prod` respectively:

    ```$xslt
    # docker-composer.yml
      database:
        container_name: mysql_database
        image: mysql:5.7
        volumes:
          - dbdata:/var/lib/mysql
        environment:
          - "MYSQL_DATABASE=nct"
          - "MYSQL_USER=phpmyadmin"
          - "MYSQL_PASSWORD=phpmyadmin"
          - "MYSQL_ROOT_PASSWORD=root."
        ports:
          - 8991:3306
          
    # .env.prod
    DB_CONNECTION=mysql
    DB_HOST=mysql_database
    DB_PORT=3306
    DB_DATABASE=nct
    DB_USERNAME=phpmyadmin
    DB_PASSWORD=phpmyadmin
    
    ```

4. Run docker-compose
    ```$xslt
    docker-compose up -d --build database && docker-compose up -d --build app && docker-compose up -d --build web
    ```

5. Visit `http://127.0.0.1:8990/` to check it worked or not. (8990 is the port of `nginx web`, change it if you has changed `nginx web` in `docker-compose.yml` file)

## How to use


1. Add a NCT playlist url to crawl.
    ```$xslt
        # If you want to crawl `https://www.nhaccuatui.com/playlist/nhac-tre-moi.html`
        Visit http://127.0.0.1:8990/playlist/crawl?url=https://www.nhaccuatui.com/playlist/nhac-tre-moi.html
    ``` 
    
    ***Note:*** 
    - When you visit this page, the system will add a `queue job` to crawl your url. The queue will auto restart
    with a `delay time = 60 seconds`, so be patient if you url has not been crawled. When the queue run through you url,
    it will get all the playlist link in your url and add to `playlists` table in database, but just only playlist's links.
    - There is another queue, called `crawl_detail:nct`. This queue is responsible for crawling playlist's detail information,
    such as `playlist name`, `playlist artists`, `playlist image` and all `media items` in the playlist.

2. Show all current playlist
    ```$xslt
        Just visit `http://127.0.0.1:8990/playlist`.
        You can see a table which show all current playlists
    ```

3. Show all playlist's song
    ```$xslt
        In the playlists table you saw on step 2, click on the name of any playlist you want.
        Now you can see a table of all songs belonged to the playlist.
    ```
    
4. Download the song
    ```$xslt
        In the sóng table you saw on step 3, click on the name of any song you want to download.
        Wait for the file to download and open to enjoy.
    ```
    
## Update.
1. PHPMyAdmin
    Recently, i add service `phpmyadmin` to make database display easier on web. Be sure that, `phpmyadmin`'s
    environment database connection information matching with database connection information in service `database`.
    
## Question
1. Could we crawl hot video items from this source https://www.tiktok.com/vi/trending in a similar way? How and/or Why?

- Yes. 
- First, we can get web url and the key of the video item by selector `.video-feed-item ._ratio_wrapper>a` 
- Then we can go to the web url and get the video src and download it.
- Updating: get new video src when the old one expired, use the key to get new one, the data response look like this
    ```angularjs
        {"@context":"http://schema.org/","@type":"VideoObject","name":"The Rock(@therock) on TikTok: @imkevinhart is getting nothing but coal this year. #badsanta","description":"The Rock(@therock) has created a short video on TikTok with music All I Want for Christmas is YOU. @imkevinhart is getting nothing but coal this year. #badsanta","thumbnailUrl":["https://p16.muscdn.com/obj/tos-maliva-p-0068/c29d8b3c139a72d27dd2bd870b049f8c","https://p16.muscdn.com/obj/tos-maliva-p-0068/688146df3ce74a5fa737f6cd4ef0130d_1576801099"],"uploadDate":"2019-12-20T00:18:17.000Z","contentUrl":"https://v16.muscdn.com/51af0b41f16f3dd5a973e2fc4ad3cff5/5e02d5de/video/tos/maliva/tos-maliva-v-0068/ea38bddece7949f9a375d426a1c8d775/?a=1233&br=3452&bt=1726&cr=0&cs=0&dr=0&ds=3&er=&l=201912242121560101151761380FA658C4&lr=tiktok_m&qs=0&rc=anQ3aHR2NGo0cTMzZTczM0ApNjpkZGlnOmRoNzxnaDlnZWdzbmlzc20xM3JfLS1hMTZzc2AuMzJhYy9fNTBjNjFhYGA6Yw%3D%3D","embedUrl":"https://www.tiktok.com/embed/6772309129626160389","keywords":"therock, The Rock, badsanta,크리스마스우와","commentCount":"35526","interactionCount":"13153636","duration":"PT10S","audio":{"name":"All I Want for Christmas is YOU - plottwist","author":"plottwist","mainEntityOfPage":{"@type":"ItemPage","@id":"https://www.tiktok.com/music/All-I-Want-for-Christmas-is-YOU-172594401737605120"}},"width":720,"height":1280,"mainEntityOfPage":{"@type":"ItemPage","@id":"https://www.tiktok.com/@therock/video/6772309129626160389"},"author":{"@type":"Person","name":"The Rock","alternateName":"therock","mainEntityOfPage":{"@type":"ProfilePage","@id":"https://www.tiktok.com/@therock"}}}
    ```
  New video src is stored in `contentUrl`.    
