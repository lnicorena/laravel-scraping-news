<?php

namespace App\Services\Scraper;

class WebScraper
{

    private $scraper;
    private $daysAgo;

    public function __construct(ScraperInterface $scraper, int $daysAgo)
    {
        $this->scraper = $scraper;
        $this->daysAgo = $daysAgo;
    }

    public function run()
    {

        $article = $this->scraper->getNextArticle();

        while ($article && $this->_shouldBeStored($article['date'])) {
            
            $article = $this->scraper->getNextArticle();
            
            print_r($article);
            // Save to DB
        }
        
    }


    private function _shouldBeStored($date)
    {
        $now = new \DateTime(date('Y-m-d'));
        $pub = new \DateTime(date('Y-m-d', strtotime($date)));

        $interval = $pub->diff($now);

        return $interval->format('%r%a') <= $this->daysAgo;
    }
}
