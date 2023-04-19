<?php
namespace process;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Intents;
use Discord\WebSockets\Event;

class DiscordProcess
{
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
                    'nonce' => $message->nonce,
                    'attachments' => $attachments_array,
                    'components' => $components_array,
                ];
                var_dump($data);
                echo "{$message->author->username}: {$message->content}", PHP_EOL;
                // Note: MESSAGE_CONTENT intent must be enabled to get the content if the bot is not mentioned/DMed.
            });
            $discord->on(Event::MESSAGE_UPDATE, function (Message $message, Discord $discord) {
                if($message->author->username === 'Midjourney Bot'){
                    $content = $message->content;
                    preg_match('/\((\d{1,3}%)\)/', $content, $percentage);
                    $percentage = $percentage[1]; //获取%比
                    if($percentage){
                        $data = [
                            'message' => $message->getRepositoryAttributes(),
                            'nonce' => $message->nonce,
                            'progress' => $percentage
                        ];
                        var_dump($data);
                    }
                    
                }
                echo "{$message->author->username}: {$message->content}", PHP_EOL;
                // Note: MESSAGE_CONTENT intent must be enabled to get the content if the bot is not mentioned/DMed.
            });

        });
        
        $discord->run();
    }

}