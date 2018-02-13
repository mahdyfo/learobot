<?php

namespace App\Repositories;

use Telegram\Bot\Api;

class Telegram
{
    public $chat_id;
    public $update;

    private $telegram;

    public function __construct(Api $client)
    {
        $this->telegram = $client;
        $this->update = $client->getWebhookUpdates();
        $this->chat_id = $this->update->getMessage()->getChat()->getId();
    }

    public function sendMessage($text, $reply = false)
    {
        $params['chat_id'] = $this->chat_id;
        $params['text'] = $text;
        if ($reply) {
            $params['reply_to_message_id'] = $this->telegram->getWebhookUpdates()->getMessage()->get('message_id');
        }

        return $this->telegram->sendMessage($params);
    }
}