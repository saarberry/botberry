<?php

namespace App\Services\Discord;

use Illuminate\Contracts\Support\Arrayable;

class Bot
{
    protected Api $api;

    public function __construct(
        public string $token,
        public string $id,
    ) {
        $this->api = new Api(id: $id);
        $this->api->asBot($token);
    }

    public function commands()
    {
        $response = $this->api->request()->get('commands');
        return $response->json();
    }

    public function registerCommand(array $command)
    {
        $response = $this->api->request()->post('commands', $command);
        return $response->json();
    }
}
