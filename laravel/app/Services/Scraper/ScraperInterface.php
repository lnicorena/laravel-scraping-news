<?php

namespace App\Services\Scraper;

interface ScraperInterface
{

    /**
     * Get the identifier (form database) of the news source
     *
     * @return Integer $id
     */
    public function getSourceID();

    /**
     * Get the next scraped article from the news source
     *
     * @return Array ['original_id', 'date_pub', 'date_mod', 
     *                'slug', 'title', 'link', 'content', 
     *                'excerpt', 'featured', 'image']
     */
    public function getNextArticle();

    /**
     * Get information about the author
     *
     * @param  Integer  $author_id
     * @return Array [
     *               'name', 'slug', 'position', 'description',
     *               'avatar', 'twitter', 'linkedin', 'facebook'
     *         ]
     */
    public function getAuthorDetails($author_id);

    /**
     * Get information about the category
     *
     * @param  Integer  $categ_id
     * @return Array [ 'name', 'slug', 'description' ];
     */
    public function getCategoryDetails($categ_id);


}
