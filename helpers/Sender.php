<?php
namespace helpers;

use GuzzleHttp\Client;

class Sender
{
    private string $channelid;
    private string $authorization;
    private string $application_id;
    private string $guild_id;
    private string $session_id;
    private string $version;
    private string $id;
    private string $flags;

    private static ?Sender $instance = null;

    private function __construct(
        string $channelid,
        string $authorization,
        string $application_id,
        string $guild_id,
        string $session_id,
        string $version,
        string $id,
        string $flags
    ) {
        $this->channelid = $channelid;
        $this->authorization = $authorization;
        $this->application_id = $application_id;
        $this->guild_id = $guild_id;
        $this->session_id = $session_id;
        $this->version = $version;
        $this->id = $id;
        $this->flags = $flags;
    }

    public static function getInstance(
        string $channelid,
        string $authorization,
        string $application_id,
        string $guild_id,
        string $session_id,
        string $version,
        string $id,
        string $flags
    ): Sender {
        if (self::$instance === null) {
            self::$instance = new Sender($channelid, $authorization, $application_id, $guild_id, $session_id, $version, $id, $flags);
        }

        return self::$instance;
    }

    public function send(string $prompt): void
    {
        $header = [
            'authorization' => $this->authorization,
        ];

        $prompt = str_replace('_', ' ', $prompt);
        $prompt = preg_replace('/\s+/', ' ', $prompt);
        $prompt = preg_replace('/[^a-zA-Z0-9\s]+/', '', $prompt);
        $prompt = strtolower($prompt);

        $payload = [
            'type' => 2,
            'application_id' => $this->application_id,
            'guild_id' => $this->guild_id,
            'channel_id' => $this->channelid,
            'session_id' => $this->session_id,
            'data' => [
                'version' => $this->version,
                'id' => $this->id,
                'name' => 'imagine',
                'type' => 1,
                'options' => [['type' => 3, 'name' => 'prompt', 'value' => $prompt . ' ' . $this->flags]],
                'attachments' => [],
            ],
        ];

        $client = new Client([
            'headers' => $header,
            'proxy' => 'http://127.0.0.1:7890'
        ]);
        
        do {
            $response = $client->post('https://discord.com/api/v9/interactions', ['json' => $payload]);
        } while ($response->getStatusCode() != 204);

        echo "prompt [{$prompt}] successfully sent!\n";
    }
}
