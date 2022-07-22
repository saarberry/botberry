<?php

namespace App\Console\Commands;

use App\Blissabot\Blissabot;
use Illuminate\Console\Command;

class DiscordCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discord:commands';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register, remove, or list the Discord bot commands.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $bot = new Blissabot(token: env('DISCORD_TOKEN'), id: env('DISCORD_APP_ID'));
        $result = $bot->commands();
        dump($result);

        // $app = Discord::app(env('DISCORD_APP_ID'));

        // $commands = [
        //     new DiscordImdbCommand(),
        // ];

        // foreach ($commands as $command) {
        //     if (!$app->hasGlobalCommand($command)) {
        //         $app->registerGlobalCommand($command);
        //     }
        // }

        return 0;
    }
}
