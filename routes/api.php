<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

enum InteractionType: int
{
    case PING = 1;
    case APPLICATION_COMMAND = 2;
    case MESSAGE_COMPONENT = 3;
    case APPLICATION_COMMAND_AUTOCOMPLETE = 4;
    case MODAL_SUBMIT = 5;
}

enum InteractionCallbackType: int
{
    case PONG = 1;
    case CHANNEL_MESSAGE_WITH_SOURCE = 4;
    case DEFERRED_CHANNEL_MESSAGE_WITH_SOURCE = 5;
    case DEFERRED_UPDATE_MESSAGE = 6;
    case UPDATE_MESSAGE = 7;
    case APPLICATION_COMMAND_AUTOCOMPLETE_RESULT = 8;
    case MODAL = 9;
}

Route::post('/interactions', function (Request $request) {
    if (!$request->hasHeader('X-Signature-Ed25519') || !$request->hasHeader('X-Signature-Timestamp')) {
        return response("I don't even know who you are", 401);
    }

    $signature = $request->header('X-Signature-Ed25519');
    $timestamp = $request->header('X-Signature-Timestamp');
    $publicKey = env('DISCORD_PUBLIC_KEY');

    $valid = sodium_crypto_sign_verify_detached(
        hex2bin($signature),
        "{$timestamp}{$request->getContent()}",
        hex2bin($publicKey)
    );

    if (!$valid) {
        return response("Get lost nerd.", 401);
    }

    if ($request->has('type')) {
        return match (InteractionType::from($request->input('type'))) {
            InteractionType::PING => response()->json(['type' => InteractionCallbackType::PONG]),
            InteractionType::APPLICATION_COMMAND => response()->json(['type' => InteractionCallbackType::CHANNEL_MESSAGE_WITH_SOURCE, 'data' => ['content' => '🐲']]),
            default => response("You didn't ask wtf.", 404),
        };
    }
});
