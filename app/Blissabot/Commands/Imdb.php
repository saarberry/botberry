<?php

namespace App\Blissabot\Commands;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

// use App\Services\Discord\Command;
// use App\Services\Discord\CommandOption;
// use App\Services\Discord\CommandType;

// class ImdbQuery implements CommandOption
// {
//     protected string $name = "query";
//     protected string $description = "Enter a query to search IMDb (or paste a link) to get a movie preview.";
//     protected bool $required = true;
// }

// class Movie extends Command
// {
//     protected string $name = "movie";
//     protected string $description = "Search IDMB for movies and actually get a preview.";
//     protected CommandType $type = CommandType::CHAT_INPUT;
// }

class Imdb
{
    public const JSON = [
        "name" => "imdb",
        "type" => 1,
        "description" => "Search IMDb.",
        "options" => [
            [
                "name" => "query",
                "description" => "The query to search for, or a link to an imdb page.",
                "type" => 3,
                "required" => true,
            ]
        ],
    ];
}
