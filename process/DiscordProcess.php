<?php
namespace process;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Intents;
use Discord\WebSockets\Event;
use helpers\Sender;

class DiscordProcess
{
    public function onWorkerStart()
    {
        $channelid = "1092701344449699841";
        $authorization = "MTA2NzA4MjU3NjU1ODMwNTI4MA.GhUKOG.tyJa1_F__yxsDiqyXox8g_EubtIdYye40M8u3U";
        $application_id = "936929561302675456";
        $guild_id = "1092701344009293895";
        $session_id = "9f905ea42829a92b5d86fa581ddd8d33";
        $version = "1077969938624553050";
        $id = "938956540159881230";
        $nonce = '223817256';
        $flags = "--v 5";
        $prompt = "forest, big bad wolf, unreal engine, cinematic lighting, UHD, super detail --aspect 2:3";
        Sender::getInstance($channelid, $authorization, $application_id, $guild_id, $session_id, $version, $id, $nonce, $flags)->send($prompt);

        // $discord = new Discord([
        //     'token' => config('env.discord.token'),
        //     'intents' => Intents::getDefaultIntents(),
        // ]);
        
        // $discord->on('ready', function (Discord $discord) {
        //     echo "Bot is ready!", PHP_EOL;
        
        //     // Listen for messages.
        //     $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
        //         var_dump($message);
        //         echo "{$message->author->username}: {$message->content}", PHP_EOL;
        //         // Note: MESSAGE_CONTENT intent must be enabled to get the content if the bot is not mentioned/DMed.
        //     });
        //     $discord->on(Event::MESSAGE_UPDATE, function (Message $message, Discord $discord) {
        //         var_dump($message);
        //         echo "{$message->author->username}: {$message->content}", PHP_EOL;
        //         // Note: MESSAGE_CONTENT intent must be enabled to get the content if the bot is not mentioned/DMed.
        //     });

        // });
        
        // $discord->run();
    }

}