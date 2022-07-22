<?php

namespace App\Botberry;

use App\Botberry\Commands\Imdb;
use App\Services\Discord\Bot;

class Botberry extends Bot
{
    public function registerGlobalCommands()
    {
        $this->registerCommand(Imdb::JSON);
    }
}
