<?php

namespace App\Services\Discord;

enum AuthType: string
{
    case BOT = 'Bot';
    case BEARER = 'Bearer';
}
