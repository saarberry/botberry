<?php

namespace App\Blissabot;

use App\Blissabot\Commands\Imdb;
use App\Services\Discord\Bot;

class Blissabot extends Bot
{
    public function registerGlobalCommands()
    {
        $this->registerCommand(Imdb::JSON);
    }
}
