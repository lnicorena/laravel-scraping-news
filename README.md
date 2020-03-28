# Web Scraper for content news + REST API

This application was built with Laravel and Postgres. The frontend is available in https://github.com/lnicorena/vuejs-spa-responsive-news

The API has an endpoint to retrieve the articles and another for the categories. 

The web crawler has a routine to scrape the news from a given source. Currently it only retrieves data from the TechCrunch site, but the architecture was design so we can easily add other sources of news. 


## setup

``` bash
# using docker-compose 
cd server

# rename the env file
mv .env.example .env

# generate the key for laravel backend
php artisan key:generate

# build and run the containers (api will be available at: http://127.0.0.1:8081)
docker-compose up --build -d

# execute the command to start scraping the news
docker exec -it php-fpm php artisan scrape:news TechCrunch --days-ago=1

```
