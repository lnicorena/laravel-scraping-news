<?php

namespace App\Services\Scraper;

interface ScraperInterface
{
    /**
     * Get the next scraped article from the news source
     *
     * @param  string  $path
     * @return Array $article
     */
    public function getNextArticle();
}
