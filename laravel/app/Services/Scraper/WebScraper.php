<?php

namespace App\Services\Scraper;

use \App\Articles;
use \App\Categories;
use \App\Authors;
use \App\Sources;
use \App\ScrapesLog;

class WebScraper
{


    /**
     * ScraperInterface object that is injected in the contructor
     */
    private $scraper;



    /**
     * $daysAgo tells how long the search will go
     */
    private $daysAgo;


    /**
     * Stores the Source of News ID 
     */
    private $sourceID;



    /**
     * Contructor
     * Initialize with the params injected 
     */
    public function __construct(ScraperInterface $scraper, int $daysAgo)
    {
        $this->scraper  = $scraper;
        $this->daysAgo  = $daysAgo;
        $this->sourceID = $scraper->getSourceID();
    }


    /**
     * 
     * This method executes the scraping of the articles
     * and stores in the database (for a range of days)
     */
    public function run()
    {

        //  Log information
        $log = new ScrapesLog;
        $log->source_id = $this->sourceID;
        $log->started_at = gmdate('Y-m-d H:i:s.u');
        $log_total = 0;
        $log_imported = 0;


        // Fetch the first article
        $data = $this->scraper->getNextArticle();

        // Iterates until the range of days is done or until no articles are fetched
        while ($data && $this->_shouldBeAnalyzed($data['date_pub'])) {

            $log_total++;

            // If the article is already stored in the DB, just go to the next
            if (Articles::where('slug', $data['slug'])->count()) {
                $data = $this->scraper->getNextArticle();
                continue;
            }


            // Store the article
            $article = new Articles;
            $article->source_id = $this->sourceID;
            $article->fill($data);
            $article->save();

            // Store the author(s)
            $authors = $this->_syncAuthors($data['authors']);
            $article->authors()->attach($authors);

            // Store the category(ies)
            $categories = $this->_syncCategories($data['categories']);
            $article->categories()->attach($categories);


            // Go to the next article and repeat
            $data = $this->scraper->getNextArticle();
            $log_imported++;
        }


        // save the log of this execution
        $log->articles_analyzed = $log_total;
        $log->articles_imported = $log_imported;
        $log->finished_at = gmdate('Y-m-d H:i:s.u');
        $log->save();
    }

    /**
     * Get the list of authors' IDs to be attached to the 
     * article. Creates it if doesn't exist.
     * 
     * @param Array $authors - array with the original ids 
     * @return Array local authors IDs
     */
    private function _syncAuthors($authors)
    {

        $result = [];

        foreach ($authors as $aut) {

            // if we have this author stored in the database, just get it's ID
            $storedAut = Sources::find($this->sourceID)->authors()->where('original_id', $aut);
            if ($storedAut->count()) {
                $result[] = $storedAut->first()->id;
            }
            // Fetch the author(s) information, store in DB and then return the local ID
            else {
                $data = $this->scraper->getAuthorDetails($aut);

                if (!$data) continue;

                $exists = Authors::where('slug', $data['slug']);

                $source = \App\Sources::find($this->sourceID);

                $author = null;

                if ($exists->count()) {
                    $author = $exists->first();
                    $result[] = $author->id;
                } else {
                    $author = new Authors;
                    $author->fill($data);
                    $author->save();
                    $result[] = $author->id;
                }
                $author->sources()->attach($source, ['original_id' => $aut]);
            }
        }

        return $result;
    }


    /**
     * Get the list of categories' IDs to be attached to the 
     * article. Creates it if doesn't exist.
     * 
     * @param Array $categories - array with the original ids 
     * @return Array local categories IDs
     */
    private function _syncCategories($categories)
    {

        $result = [];

        foreach ($categories as $cat) {

            // if we have this category already stored in the database, just get it's ID
            $storedCat = Sources::find($this->sourceID)->categories()->where('original_id', $cat);
            if ($storedCat->count()) {
                $result[] = $storedCat->first()->id;
            }
            // Fetch the category(ies) information, store in DB and then return the local ID
            else {

                $data = $this->scraper->getCategoryDetails($cat);

                if (!$data) continue;

                $exists = Categories::where('slug', $data['slug']);

                $source = \App\Sources::find($this->sourceID);

                $category = null;

                if ($exists->count()) {
                    $category = $exists->first();
                    $result[] = $category->id;
                } else {
                    $category = new Categories;
                    $category->fill($data);
                    $category->save();
                    $result[] = $category->id;
                }
                $category->sources()->attach($source, ['original_id' => $cat]);
            }
        }

        return $result;
    }


    /**
     * Check if a given date is in the range ($this->daysAgo)
     * that shoul be analyzed.
     * 
     * @param DateTime $date
     * @return Bolean
     */
    private function _shouldBeAnalyzed($date)
    {
        $now = new \DateTime(date('Y-m-d'));
        $pub = new \DateTime(date('Y-m-d', strtotime($date)));

        $interval = $pub->diff($now);

        return $interval->format('%r%a') <= $this->daysAgo;
    }
}
