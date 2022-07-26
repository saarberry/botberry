<?php

namespace App\Services\Imdb;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Support\Arrayable;

class Title implements Arrayable
{
    public string $name;
    public string $description;
    public int $releaseYear;

    public string $rating;
    public string $voteCount;

    public int $durationHours;
    public int $durationMinutes;

    public array $directors = [];
    public array $stars = [];

    public string $url;
    public string $imageUrl;

    public function __construct(array $data = null)
    {
        if ($data) {
            $this->name = $data['name'];
            $this->releaseYear = Carbon::parse($data['datePublished'])->year;
            $this->description = $data['description'];

            $this->rating = $data['aggregateRating']['ratingValue'];
            $this->voteCount = $data['aggregateRating']['ratingCount'];

            $duration = CarbonPeriod::create($data['duration']);
            $this->durationHours = $duration->interval->hours;
            $this->durationMinutes = $duration->interval->minutes;

            $this->url = Scraper::URL . $data['url'];
            $this->imageUrl = $data['image'];
        }
    }

    public function addDirector(Person $director)
    {
        $this->directors[] = $director;
    }

    public function addStar(Person $star)
    {
        $this->stars[] = $star;
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
