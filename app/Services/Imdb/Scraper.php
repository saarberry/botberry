<?php

namespace App\Services\Imdb;

use App\Services\Imdb\Exceptions\UrlNotImdbException;
use App\Services\Imdb\Exceptions\UrlNotImdbTitleException;
use DOMDocument;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use UnhandledMatchError;

class Scraper
{
    const URL = "https://www.imdb.com/";


    public function parse(string $url)
    {
        try {
            return match (true) {
                $this->isTitleUrl($url) => $this->scrapeTitle($url),
            };
        } catch (UnhandledMatchError $e) {
            throw new UrlNotImdbException();
        }
    }

    /**
     * Retrieve a title from IMDb.
     *
     * @param string $url
     * @return string
     */
    public function scrapeTitle(string $url)
    {
        // Fetch the page.
        $id = $this->titleIdFromUrl($url);
        $body = $this->request()->get("title/{$id}")->body();

        // Get rid of some trailing ampersands.
        $body = preg_replace("/&(?!\S+;)/", "&amp;", $body);

        // For real why does it break on html5.
        libxml_use_internal_errors(true);

        // Parse contents of the response.
        $dom = new DOMDocument();
        $dom->loadHTML($body);
        libxml_clear_errors();

        $scripts = $dom->getElementsByTagName('script');

        // Find the LD JSON dataset.
        foreach ($scripts as $script) {
            if ($script->getAttribute("type") == "application/ld+json") {
                return json_decode($script->textContent, true);
            }
        }
    }

    /**
     * Is the given URL an IMDb title?
     *
     * @param string $url
     * @return boolean
     */
    protected function isTitleUrl(string $url): bool
    {
        try {
            $this->titleIdFromUrl($url);
        } catch (UrlNotImdbTitleException $e) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve the title ID (tt0123456) from the given IMDb url.
     *
     * @param string $url
     * @return string
     * @throws UrlNotImdbTitleException
     */
    protected function titleIdFromUrl(string $url): string
    {
        if (preg_match("/imdb\.com\/title\/(tt\d{7})/", $url, $matches)) {
            return $matches[1];
        }

        throw new UrlNotImdbTitleException();
    }

    /**
     * Blank request object to start scraping IMDb endpoints.
     *
     * @return PendingRequest
     */
    protected function request(): PendingRequest
    {
        return Http::baseUrl(static::URL);
    }
}
