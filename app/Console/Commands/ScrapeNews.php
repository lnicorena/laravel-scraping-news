<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Tests\Unit\ExampleTest;
use App\ScrapeCommandError;

use App\Services\Scraper\WebScraper;

class ScrapeNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:news {source} {--days-ago=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Searches and scrapes news items for a given source';

    /**
     * The available options for the {source} param.
     *
     * @var Array
     */
    protected $availableSources = [
        'TechCrunch'
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        try {

            $source = $this->argument('source');
            $daysAgo = \intval($this->option('days-ago'));
            $path = "App\\Services\\Scraper";
            $className = "{$path}\\{$source}";

            if (!\in_array($source, $this->availableSources))
                throw new ScrapeCommandError('Source param is incorrect or not implemented. Allowed sources: ' . \implode(' | ', $this->availableSources));

            if ($daysAgo < 0)
                throw new ScrapeCommandError('days-ago param can not be lower than zero.');

            if (! \in_array ("{$path}\\ScraperInterface", class_implements ($className)) )
                throw new ScrapeCommandError('The source requested is not correctly implemented.');


            $scraper = new $className();

            $handler = new WebScraper($scraper, $daysAgo);
            

            $handler->run();
        } catch (ScrapeCommandError $e) {
            echo "ERROR: {$e->getMessage()}\n";
        }
    }
}
