<?php

namespace App\Console\Commands;

use App\Services\Imdb\Scraper;
use Illuminate\Console\Command;

class ImdbScrape extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imdb:scrape {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape information about a title from IMDb, given that the qualified URL is supplied.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $scraper = new Scraper();
        $url = $this->argument('url');
        dd($scraper->parse($url));

        return 0;
    }
}
