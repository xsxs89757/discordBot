<?php
namespace process;

use support\Log;
use Discord\Discord;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Discord\Parts\Channel\Message;

use Ratchet\Client\Connector as RatchetConnector;
use React\EventLoop\Loop;
use React\Socket\Connector as ReactConnector;
use Ratchet\MessageComponentInterface;

class DiscordProcess
{
    public function onWorkerStart()
    {
        $loop = Loop::get();
        $reactConnector = new ReactConnector($loop, [
            'timeout' => 500, // 设置超时时间为 10 秒
        ]);
        $connector = new RatchetConnector($loop, $reactConnector);
        $connector("wss://gateway.discord.gg/?encoding=json&v=9")
            ->then(function(\Ratchet\Client\WebSocket $conn){
                
                $conn->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn) {
                    echo "Text message received: {$msg}\n";
                });
                $conn->send('{"op":6,"d":{"token":"MTA2NzA4MjU3NjU1ODMwNTI4MA.GfARvy.scMJWMo_jlYv5JBKgDlTWVvk8aVuLpOTAoJnkA","session_id":"c5345bfe9230d9df890b9190fb2a5561","seq":438}}');
                $conn->on('close', function($code = null, $reason = null)  {
                    echo "Connection closed ({$code} - {$reason})\n";
                });
            }, function(\Exception $e) use ($loop) {
                echo "Could not connect: {$e->getMessage()}\n";

            });

        $loop->run();

        $discord = new Discord([
            'token' => config('env.discord.token'),
            'intents' => Intents::getDefaultIntents(),
        ]);
        
        $discord->on('ready', function (Discord $discord) {
            echo "Bot is ready!", PHP_EOL;
            // Listen for messages.
            $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
                $attachments_array = [];
                $components_array = [];
                if (count($message->attachments) > 0) {
                    // 遍历所有附件
                    foreach ($message->attachments as $attachment) {
                        // 提取附件的相关信息
                        $attachments_array[] = [
                            'id' => $attachment->id,
                            'filename' => $attachment->filename,
                            'url' => $attachment->url,
                            'proxy_url' => $attachment->proxy_url,
                            'size' => $attachment->size,
                            'height' => $attachment->height,
                            'width' => $attachment->width
                        ];
                    }
                }
                if (count($message->components) > 0) {
                    // 遍历所有组件行
                    foreach ($message->components as $row) {
                        $row_components = [];
                        // 遍历行内的每个组件
                        foreach ($row->components as $component) {
                            // 提取组件的相关信息
                            $row_components[] = [
                                'type' => $component->type,
                                'style' => $component->style ?? null,
                                'label' => $component->label ?? null,
                                'emoji' => $component->emoji ? ($component->emoji->name) : null,
                                'custom_id' => $component->custom_id ?? null,
                                'url' => $component->url ?? null,
                                'disabled' => $component->disabled ?? null,
                            ];
                        }
        
                        $components_array[] = $row_components;
                    }
                }
                $data = [
                    'message' => $message->getRepositoryAttributes(),
                    'uuid' => pregGetUUID($message->content),
                    'attachments' => $attachments_array,
                    'components' => $components_array,
                ];
                
                Log::debug('MESSAGE_CREATE:', json_decode(json_encode($message), true));
                // var_dump($message);
                echo "{$message->author->username}: {$message->content}", PHP_EOL;
                // Note: MESSAGE_CONTENT intent must be enabled to get the content if the bot is not mentioned/DMed.
            });
            $discord->on(Event::MESSAGE_UPDATE, function (Message $message, Discord $discord) {
                if($message->author->username === 'Midjourney Bot'){
                    $content = $message->content;
                    $percentageb = '';
                    preg_match('/\((\d{1,3}%)\)/', $content, $percentage);
                    if($percentage)$percentageb = $percentage[1]; //获取%比
                    if($percentage){
                        $data = [
                            'message' => $message->getRepositoryAttributes(),
                            'uuid' => pregGetUUID($message->content),
                            'progress' => $percentageb
                        ];
                    }
                    
                }
                Log::debug('MESSAGE_UPDATE:', json_decode(json_encode($message), true));
                echo "{$message->author->username}: {$message->content}", PHP_EOL;
                // Note: MESSAGE_CONTENT intent must be enabled to get the content if the bot is not mentioned/DMed.
            });

        });
        
        $discord->run();
    }

}