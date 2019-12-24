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
