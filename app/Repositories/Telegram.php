<?php

namespace App\Repositories;

use Telegram\Bot\Api;

class Telegram
{
    public $chat_id;
    public $update;

    private $telegram;

    public function __construct()
    {
        $this->telegram = new Api;
        $this->update = $this->telegram->getWebhookUpdates();
        $this->chat_id = $this->update->getMessage()->getChat()->getId();
    }

    public function sendMessage($text)
    {
        return $this->telegram->sendMessage(['chat_id' => $this->chat_id, 'text' => $text]);
    }
}