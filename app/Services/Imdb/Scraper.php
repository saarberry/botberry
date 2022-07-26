<?php

namespace App\Services\Imdb;

use App\Services\Imdb\Exceptions\UrlNotImdbException;
use App\Services\Imdb\Exceptions\UrlNotImdbResourceException;
use DOMDocument;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use UnhandledMatchError;

class Scraper
{
    const URL = "https://www.imdb.com";


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
        $id = $this->idFromUrl($url);
        $dom = $this->getDOMFromUrl("/title/tt{$id}");

        $data = $this->getLdJsonFromDOM($dom);
        $title = new Title($data);

        foreach ($data['actor'] as $actor) {
            $actorDom = $this->getDOMFromUrl($actor['url']);
            $actorData = $this->getLdJsonFromDOM($actorDom);
            $person = new Person($actorData);

            foreach ($this->getKnownForTitlesFromDOM($actorDom) as $knownFor) {
                $person->addTitle($knownFor);
            }

            $title->addStar($person);
        }

        foreach ($data['director'] as $director) {
            $directorDom = $this->getDOMFromUrl($director['url']);
            $directorData = $this->getLdJsonFromDOM($directorDom);
            $person = new Person($directorData);

            foreach ($this->getKnownForTitlesFromDOM($directorDom) as $knownFor) {
                $person->addTitle($knownFor);
            }

            $title->addDirector($person);
        }

        return $title->toArray();
    }

    /**
     * Get the titles from the known for section.
     *
     * @param DOMDocument $dom
     * @return array
     */
    protected function getKnownForTitlesFromDOM(DOMDocument $dom): array
    {
        $el = $dom->getElementById('knownfor');
        $titles = [];

        foreach ($el->getElementsByTagName('a') as $link) {
            if (preg_match("/[^\s]+/", $link->textContent)) {
                $title = new Title();
                $title->name = $link->textContent;
                $title->url = static::URL . $link->getAttribute('href');
                $titles[] = $title;
            }
        }

        return $titles;
    }

    /**
     * Retrieve the LD JSON set from the given DOM.
     *
     * @param DOMDocument $dom
     * @return array
     */
    protected function getLdJsonFromDOM(DOMDocument $dom): array
    {
        $scripts = $dom->getElementsByTagName('script');

        foreach ($scripts as $script) {
            if ($script->getAttribute("type") == "application/ld+json") {
                return json_decode($script->textContent, true);
            }
        }

        return [];
    }

    /**
     * Read the DOM from the given URL.
     *
     * @param string $url
     * @return DOMDocument
     */
    protected function getDOMFromUrl(string $url): DOMDocument
    {
        $body = $this->request()->get($url)->body();

        // Get rid of some trailing ampersands.
        $body = preg_replace("/&(?!\S+;)/", "&amp;", $body);

        // For real why does it break on html5.
        libxml_use_internal_errors(true);

        // Parse contents of the response.
        $dom = new DOMDocument();
        $dom->loadHTML($body);
        libxml_clear_errors();

        return $dom;
    }

    /**
     * Is the given URL an IMDb title?
     *
     * @param string $url
     * @return boolean
     */
    protected function isTitleUrl(string $url): bool
    {
        return Str::contains($url, "imdb.com/title/tt");
    }

    /**
     * Is the given URL an IMDb person?
     *
     * @param string $url
     * @return boolean
     */
    protected function isPersonUrl(string $url): bool
    {
        return Str::contains($url, "imdb.com/name/nm");
    }

    /**
     * Retrieve the  ID (0123456) from the given IMDb url.
     *
     * @param string $url
     * @return string
     * @throws UrlNotImdbResourceException
     */
    protected function idFromUrl(string $url): string
    {
        if (preg_match("/imdb\.com\/(title\/tt|name\/nm)(\d{7})/", $url, $matches)) {
            return $matches[2];
        }

        throw new UrlNotImdbResourceException();
    }

    /**
     * Blank request object to start scraping IMDb endpoints.
     *
     * @return PendingRequest
     */
    protected function request(): PendingRequest
    {
        return Http::baseUrl(static::URL)
            ->withHeaders(['accept-language' => 'en-US']);
    }
}
