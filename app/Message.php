<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message as TMessage;

class Message extends Model
{
    public $decoded_message;
    public $text = '';
    public $chat_id;
    public $message_id;

    public $is_raw_text;
    public $is_photo;
    public $is_audio;
    public $is_voice;
    public $is_video;
    public $is_document;
    public $is_sticker;

    public $is_reply;
    public $replied_message_text = '';
    public $is_reply_to_me;
    public $self_reply;

    /**
     * Message constructor.
     * @param Api $telegram
     * @internal param string $message
     */

    public function __construct(Api $telegram)
    {
        parent::__construct();

        $this->decoded_message = $telegram->getWebhookUpdates()->getMessage();

        if ($this->decoded_message) {
            $this->parse($this->decoded_message);
        }
    }

    /**
     * Message parse
     * @param $message
     */
    private function parse(TMessage $message)
    {
        //Message Type
        $this->is_photo = !empty($message->getPhoto());
        $this->is_audio = !empty($message->getAudio());
        $this->is_voice = !empty($message->getVoice());
        $this->is_video = !empty($message->getVideo());
        $this->is_document = !empty($message->getDocument());
        $this->is_sticker = !empty($message->getSticker());
        $this->is_raw_text = !empty($message->getText());

        //Message Attributes
        $this->is_reply = !empty($message->getReplyToMessage());
        $this->is_reply_to_me = (
        $this->is_reply ?
            ($message->getReplyToMessage()->getFrom()->getUsername() == config('settings.bot_id')) : false
        );
        $this->self_reply = $this->is_reply ? $this->is_reply && ($message->getReplyToMessage()->getFrom()->getUsername() == $message->getFrom()->getUsername()) : false;
        $this->chat_id = $message->getChat()->getId();
        $this->message_id = $this->decoded_message->get('message_id');

        //Message Text
        if (!empty($message->getText())) {
            $this->text = $message->getText();
        } elseif (!empty($message->getCaption())) {
            $this->text = $message->getCaption();
        } elseif (!empty($message->getSticker())) {
            $this->text = $message->getSticker()->get('emoji');
        }

        //Reply Message Text
        if($this->is_reply) {
            if (!empty($message->getReplyToMessage()->getText())) {
                $this->replied_message_text = $message->getReplyToMessage()->getText();
            } elseif (!empty($message->getReplyToMessage()->getCaption())) {
                $this->replied_message_text = $message->getReplyToMessage()->getCaption();
            }
        }
    }


}
