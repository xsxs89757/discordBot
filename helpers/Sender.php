<?php
namespace helpers;

use GuzzleHttp\Client;
// $channelid = "1092701344449699841";
//         $authorization = "MTA2NzA4MjU3NjU1ODMwNTI4MA.GhUKOG.tyJa1_F__yxsDiqyXox8g_EubtIdYye40M8u3U";
//         $application_id = "936929561302675456";
//         $guild_id = "1092701344009293895";
//         $session_id = "9f905ea42829a92b5d86fa581ddd8d33";
//         $version = "1077969938624553050";
//         $id = "938956540159881230";
//         $nonce = '223817256';
//         $flags = "--v 5";
//         $prompt = "forest, big bad wolf, unreal engine, cinematic lighting, UHD, super detail --aspect 2:3";
//         Sender::getInstance($channelid, $authorization, $application_id, $guild_id, $session_id, $version, $id, $nonce, $flags)->send($prompt);

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
    private string $nonce;

    const IMAGINE = 'imagine';
    const FAST = 'fast';
    const RELAX = 'relax';

    const USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36';

    private static ?Sender $instance = null;

    /**
     * 构造函数
     *
     * @param string $channelid
     * @param string $authorization
     * @param string $application_id
     * @param string $guild_id
     * @param string $session_id
     * @param string $version
     * @param string $id
     * @param string $nonce
     * @param string $flags
     */
    private function __construct(
        string $channelid,
        string $authorization,
        string $application_id,
        string $guild_id,
        string $session_id,
        string $version,
        string $id,
        string $nonce,
        string $flags = '--v 5'
    ) {
        $this->channelid = $channelid;
        $this->authorization = $authorization;
        $this->application_id = $application_id;
        $this->guild_id = $guild_id;
        $this->session_id = $session_id;
        $this->version = $version;
        $this->id = $id;
        $this->nonce = $nonce;
        $this->flags = $flags;
    }

    /**
     * 单例
     *
     * @param string $channelid
     * @param string $authorization
     * @param string $application_id
     * @param string $guild_id
     * @param string $session_id
     * @param string $version
     * @param string $id
     * @param string $nonce
     * @param string $flags
     * @return Sender
     */
    public static function getInstance(
        string $channelid,
        string $authorization,
        string $application_id,
        string $guild_id,
        string $session_id,
        string $version,
        string $id,
        string $nonce,
        string $flags
    ): Sender {
        if (self::$instance === null) {
            self::$instance = new Sender($channelid, $authorization, $application_id, $guild_id, $session_id, $version, $id, $nonce, $flags);
        }

        return self::$instance;
    }

    /**
     * 切换fast状态
     *
     * @return string
     */
    public function switchModeFast() : string {
        return $this->request(self::FAST);
    }

    /**
     * 切换relax状态
     *
     * @return string
     */
    public function switchModeRelax() : string {
        return $this->request(self::RELAX);
    }

    /**
     * 发送消息
     *
     * @param string $prompt
     * @return string
     */
    public function send(string $prompt): string
    {
        return $this->request(self::IMAGINE, $prompt);
    }

    /**
     * todo:发送消息 
     *
     * @param string $type
     * @param string|null $prompt
     * @return string
     */
    private function request(string $type, string $prompt = null) : string{
        $header = [
            'authorization' => $this->authorization,
            'user-agent' => self::USER_AGENT
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
                'name' => $type,
                'type' => 1,
                'options' => $type === 'imagine' ? [
                    ['type' => 3, 'name' => 'prompt', 'value' => $prompt . ' ' . $this->flags]
                ]: [],
                'attachments' => [],
            ],
            'nonce' => $this->nonce,
        ];

        $client = new Client([
            'headers' => $header,
            'proxy' => 'http://127.0.0.1:7890'
        ]);
        $attempt = 0;
        $maxAttempts = 3;
        do {
            $response = $client->post('https://discord.com/api/v9/interactions', ['json' => $payload]);
            $attempt++;

            if ($attempt > $maxAttempts) {
                throw new \Exception("Exceeded maximum attempts ($maxAttempts) to send payload.");
            }
        } while ($response->getStatusCode() != 204);

        return $type === 'imagine' ? $prompt  . ' ' . $this->flags : '';
    }
}
