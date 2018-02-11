<?php

namespace App\Http\Controllers;

use App\Message;
use App\Repositories\Predict;
use App\Repositories\Telegram;
use App\Repositories\Train;
use Telegram\Bot\Api;

class MessageController extends Controller
{
    public function hook()
    {
        $telegram = new Api;

        //Instantiate Message
        $message = new Message($telegram);

        //Skip null messages
        if (empty($message->text)) {
            return null;
        }

        //Train AI
        if ($message->is_reply && !$message->is_reply_to_me && !$message->self_reply) {
            new Train($message);
        }

        //Predict
        $predict = new Predict($message);
        if ($message->is_reply_to_me) {
            $reply = $predict->getMostRelativeReply(1.4);
        } else {
            $reply = $predict->getMostRelativeReply(); //default min repeat
        }

        //Send Response
        if (!empty($reply)) {
            $client = new Telegram($telegram);
            $client->sendMessage($reply, 1);
        }
    }
}
