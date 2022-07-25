<?php

namespace App\Services\Imdb;

use Illuminate\Contracts\Support\Arrayable;

class Title implements Arrayable
{
    public function __construct(
        public string $name,
        public int $releaseYear,
        public string $description,
        public string $duration,
        public string $rating,
        public string $voteCount,
        public array $directors,
        public array $stars,
    ) {
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
