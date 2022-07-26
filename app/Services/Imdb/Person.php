<?php

namespace App\Services\Imdb;

use Illuminate\Contracts\Support\Arrayable;

class Person implements Arrayable
{
    public string $name;

    public string $url;
    public string $imageUrl;

    public array $titles = [];

    public function __construct(array $data = null)
    {
        if ($data) {
            $this->name = $data['name'];
            $this->url = Scraper::URL . $data['url'];
            $this->imageUrl = $data['image'];
        }
    }

    public function addTitle(Title $title)
    {
        $this->titles[] = $title;
    }

    /**
     * Convert to array, maybe useful idk whatever.
     *
     * @return array
     */
    public function toArray()
    {
        return (array) $this;
    }
}
