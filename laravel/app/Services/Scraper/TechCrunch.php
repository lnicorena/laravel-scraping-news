<?php

namespace App\Services\Scraper;

use GuzzleHttp\Client;


class TechCrunch implements ScraperInterface
{

    /**
     * The Internal identifier of this 
     * source of news (Tech Crunch)
     */
    const SOURCE_NEWS_ID = 1;

    /**
     * State control for the method getNextArticle()
     */
    protected $next = 0;

    /**
     * The current page that is being fetched by _loadArticles()
     *
     */
    protected $page = 1;

    /**
     * The articles loadeds from the API
     */
    protected $articles = [];

    /**
     * API base url to fetch the informations
     */
    protected $base_url = 'https://techcrunch.com/wp-json/wp/v2';


    /**
     * GuzzleHttp\Client used to make the requests
     */
    protected $client;


    /**
     * Class contructor
     */ 
    public function __construct()
    {
        // Initialize the http client
        $this->client = new Client(['http_errors' => false]);

        // Load the articles
        $this->_loadArticles();
    }

    /**
     * Get the identifier (form database) of the news source
     *
     * @return Integer $id
     */
    public function getSourceID()
    {
        return self::SOURCE_NEWS_ID;
    }

    /**
     * Get the next scraped article from the news source
     *
     * @return Array ['original_id', 'date_pub', 'date_mod', 
     *                'slug', 'title', 'link', 'content', 
     *                'excerpt', 'featured', 'image']
     */
    public function getNextArticle()
    {
        if (count($this->articles) && count($this->articles) > $this->next) {
            return $this->_prepareResponse($this->articles[$this->next++]);
        } elseif (count($this->articles)) {
            $this->next = 0;
            $this->_loadArticles();
            return $this->getNextArticle();
        } else {
            return null;
        }
    }


    /**
     * Get information about the author
     *
     * @param  Integer  $author_id
     * @return Array [
     *               'name', 'slug', 'position', 'description',
     *               'avatar', 'twitter', 'linkedin', 'facebook'
     *         ]
     */
    public function getAuthorDetails($author_id)
    {

        $response = $this->client->request('GET', $this->_getAuthorUrl($author_id));
        if ($response->getStatusCode() != 200 && $response->getStatusCode() != 304)
            return null;

        $data = json_decode($response->getBody(), true);
        return [
            'name'        => $data['name'],
            'slug'        => $data['slug'],
            'position'    => $data['position'],
            'description' => $data['description'],
            'avatar'      => $data['avatar_urls']['96'],
            'twitter'     => isset($data['links']['twitter']) ? $data['links']['twitter'] : null,
            'linkedin'    => isset($data['links']['linkedin']) ? $data['links']['linkedin'] : null,
            'facebook'    => isset($data['links']['facebook']) ? $data['links']['facebook'] : null
        ];
    }

    /**
     * Get information about the category
     *
     * @param  Integer  $categ_id
     * @return Array [ 'name', 'slug', 'description' ];
     */
    public function getCategoryDetails($categ_id)
    {

        $response = $this->client->request('GET', $this->_getCategoriesUrl($categ_id));
        if ($response->getStatusCode() != 200 && $response->getStatusCode() != 304)
            return null;

        $data = json_decode($response->getBody(), true);
        return [
            'name'        => $data['name'],
            'slug'        => $data['slug'],
            'description' => $data['description']
        ];
    }


    /**
     * Get the category of an article
     *
     * @param  Integer  $article_id
     * @return Array $categories;
     */
    public function getArticleCategories($article_id)
    {
        $response = $this->client->request('GET', $this->_getPostCategoriesUrl($article_id));
        return json_decode($response->getBody(), true);
    }


    /**
     * Fetch the next articles when it is called.
     */
    public function _loadArticles()
    {
        $response = $this->client->request('GET', $this->_getArticlesUrl($this->page));
        $this->articles = json_decode($response->getBody(), true);
        $this->page++;
    }



    /**
     * Prepare the data from the external API to be used
     *
     * @param  Array $article
     * @return Array $data;
     */
    private function _prepareResponse($article)
    {
        return [
            'original_id' => $article['id'],
            'date_pub'   => $article['date_gmt'],
            'date_mod'   => $article['modified_gmt'],
            'slug'       => $article['slug'],
            'title'      => $article['title']['rendered'],
            'link'       => $article['link'],
            'content'    => $article['content']['rendered'],
            'excerpt'    => $article['excerpt']['rendered'],
            'featured'   => $article['featured'],
            'image'      => $article['jetpack_featured_media_url'],
            'authors'    => $article['authors'],
            'categories' => $article['categories'],
        ];
    }


    /**
     * Get the URL to fetch the articles
     *
     * @param  Integer $page
     * @return String
     */
    private function _getArticlesUrl($page)
    {
        return $this->_formatUrl('/posts?page={1}', $page);
        // alternative: https://techcrunch.com/wp-json/tc/v1/magazine?page={1}
    }

    /**
     * Get the URL to fetch authors
     *
     * @param  Integer $author_id
     * @return String
     */
    private function _getAuthorUrl($author_id)
    {
        return $this->_formatUrl('/users/{1}', $author_id);
    }

    /**
     * Get the URL to fetch categories
     *
     * @param  Integer $categ_id
     * @return String
     */
    private function _getCategoriesUrl($categ_id)
    {
        return $this->_formatUrl('/categories/{1}', $categ_id);
    }

    /**
     * Get the URL to fetch article's categories
     *
     * @param  Integer $post_id
     * @return String 
     */
    private function _getPostCategoriesUrl($post_id)
    {
        return $this->_formatUrl('/categories?post={1}', $post_id);
    }


    /**
     * Helper function to format the URL string with the params
     *
     * @param  String $url
     * @param  Array ...$params
     * @return String
     */
    private function _formatUrl($url, ...$params)
    {

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
