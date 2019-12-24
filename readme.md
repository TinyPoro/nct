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
2. Update mysql database connection in `docker-compose.yml` and `.env.prod`, but be sure 
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

3. Run docker-compose
```$xslt
docker-compose up -d --build database && docker-compose up -d --build app && docker-compose up -d --build web
```

4. Check 
