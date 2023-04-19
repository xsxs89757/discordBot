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
            'token' => config('env.discord.token'),
            'intents' => Intents::getDefaultIntents(),
        ]);
        
        $discord->on('ready', function (Discord $discord) {
            echo "Bot is ready!", PHP_EOL;
        
            // Listen for messages.
            $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
                // $data = [
                //     'message' => $message->getRepositoryAttributes(),
                //     'timestamp' => $message->timestamp->toArray(),
                //     'channel_id' => $message->channel_id,
                //     'guild_id' => $message->guild_id,
                //     'attachments' => $message->attachments->toArray(),
                //     'components' => $message->components->toArray(),
                // ];
                // var_dump($data);
                echo "{$message->author->username}: {$message->content}", PHP_EOL;
                // Note: MESSAGE_CONTENT intent must be enabled to get the content if the bot is not mentioned/DMed.
            });
            $discord->on(Event::MESSAGE_UPDATE, function (Message $message, Discord $discord) {
                var_dump($message);
                echo "{$message->author->username}: {$message->content}", PHP_EOL;
                // Note: MESSAGE_CONTENT intent must be enabled to get the content if the bot is not mentioned/DMed.
            });

        });
        
        $discord->run();
    }

}