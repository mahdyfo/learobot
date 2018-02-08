<?php

namespace App\Http\Controllers;

use App\Message;
use App\Repositories\Predict;
use App\Repositories\Telegram;
use App\Repositories\Train;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function hook(Request $request)
    {
        $data = $request->getContent();
        $message = new Message($data);

        //skip null messages
        if (is_null($message)) return;

        //Train AI
        if (!$message->is_reply_to_me) {
            new Train($message);
        }

        //Predict
        $predict = new Predict($message);
        if ($message->is_reply_to_me) {
            $reply = $predict->getMostRelativeReply(1);
        } else {
            $reply = $predict->getMostRelativeReply(); //default min repeat
        }

        //Send Response
        if ($reply) {
            $telegram = new Telegram;
            $telegram->sendMessage($reply);
        }
    }
}
