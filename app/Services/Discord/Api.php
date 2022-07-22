<?php

namespace App\Services\Discord;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Enum;

class Api
{
    const URL = "https://discord.com/api/";
    const VERSION = "v10";

    protected string $token;
    protected AuthType $authType;

    public function __construct(protected string $id)
    {
    }

    /**
     * Approach the API as a bot user.
     *
     * @param string $token
     * @return void
     */
    public function asBot(string $token)
    {
        $this->token = $token;
        $this->authType = AuthType::BOT;
    }

    /**
     * Approach the API as a regular user.
     *
     * @param string $token
     * @return void
     */
    public function asUser(string $token)
    {
        $this->token = $token;
        $this->authType = AuthType::BEARER;
    }

    /**
     * Base URL for the API.
     *
     * @return string
     */
    public function baseUrl(): string
    {
        return static::URL . static::VERSION . "/applications/{$this->id}/";
    }

    /**
     * Blank request object to start crafting API requests with.
     *
     * @return PendingRequest
     */
    public function request(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl())
            ->withToken(type: 'Bot', token: $this->token);
    }
}
