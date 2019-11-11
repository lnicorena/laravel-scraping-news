<?php

namespace App\Services\Scraper;


// use Goutte\Client;
use GuzzleHttp\Client;


class TechCrunch implements ScraperInterface
{

    /*

    https://techcrunch.com/pages/rssfeeds/

    http://feeds.feedburner.com/TechCrunch/


    // all categories
    https://techcrunch.com/wp-json/tc/v1/magazine?page=1

    // Startups
    https://techcrunch.com/wp-json/tc/v1/magazine?page=1&_embed=true&_envelope=true&categories=20429&cachePrevention=0

    // Apps
    https://techcrunch.com/wp-json/tc/v1/magazine?page=1&_embed=true&_envelope=true&categories=449557102&cachePrevention=0

    // Gadgets
    https://techcrunch.com/wp-json/tc/v1/magazine?page=1&_embed=true&_envelope=true&categories=449557086&cachePrevention=0



    */

    protected $next = 0;
    protected $page = 1;

    protected $articles = [];


    protected $base_url = 'https://techcrunch.com/wp-json/wp/v2';

    
    protected $client;


    public function __construct()
    {
        $this->client = new Client();
        $this->_loadArticles();
    }

    public function _loadArticles()
    {
        $response = $this->client->request('GET', $this->_getArticlesUrl($this->page));
        // $json = $response->getBody()->getContents(); // Get json as string
        $this->articles = json_decode($response->getBody(), true);
        $this->page++;
    }


    public function getNextArticle()
    {
        if (count($this->articles) && count($this->articles) > $this->next) {
            return $this->prepareResponse($this->articles[$this->next++]);
        } elseif (count($this->articles)) {
            $this->next = 0;
            $this->_loadArticles();
            return $this->getNextArticle();
        } else {
            return null;
        }
    }

    private function prepareResponse($article)
    {
        return [
            'date' => $article['date_gmt']
        ];
    }

    private function _getArticlesUrl($page)
    {
        return $this->_formatUrl('/posts?page={1}', $page);
        // alternative: https://techcrunch.com/wp-json/tc/v1/magazine?page={1}
    }
    private function _getCategoriesUrl($post_id)
    {
        return $this->_formatUrl ('/categories?post={1}', $post_id);
    }
    private function _getAuthorUrl($author_id)
    {
        return $this->_formatUrl('/users/{1}', $author_id );
    }

    private function _formatUrl($url, ...$params) {

        $_url = $this->base_url . $url;

        if (!count($params))
            return $_url;
        
        $_params = [];
        array_walk($params, function ($v, $k) use (&$_params) {
            $key = '{' . ($k + 1) . '}';
            $_params[$key] = $v;
        });
        return strtr($_url, $_params);
        
    }
}
