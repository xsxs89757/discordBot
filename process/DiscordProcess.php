<?php
namespace process;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Intents;
use Discord\WebSockets\Event;

class DiscordProcess
{
    const PROXY = 'http://127.0.0.1:7890';

    public function onWorkerStart()
    {
        $discord = new Discord([
            'token' => 'MTA5Nzc4NDQ5NTczMTY1NDY1Ng.G46-Lx.j1V8RHTjhPb0ACnlWbYLtOOyHh4AF6VOxSLsVA',
            'intents' => Intents::getDefaultIntents(),
            'dnsConfig' => '127.0.0.1'
            // 'http' => [
            //     'proxy' => [
            //         'address' => self::PROXY,
            //     ],
            // ],
            // 'ws' => [
            //     'proxy' => [
            //         'address' => self::PROXY,
            //     ],
            // ],
        ]);
        
        $discord->on('ready', function (Discord $discord) {
            echo "Bot is ready!", PHP_EOL;
        
            // Listen for messages.
            $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
                echo "{$message->author->username}: {$message->content}", PHP_EOL;
                // Note: MESSAGE_CONTENT intent must be enabled to get the content if the bot is not mentioned/DMed.
            });
        });
        
        $discord->run();
    }

}