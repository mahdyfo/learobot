<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    /**
     * Message constructor.
     * @param string $message
     */

    public function __construct($message)
    {
        parent::__construct();

        $this->decoded_message = json_decode($message)->message;

        $this->parse($this->decoded_message);
    }

    /**
     * Message parse
     * @param $message
     */
    private function parse($message){
        //Message Type
        $this->is_photo     = isset($message->photo);
        $this->is_audio     = isset($message->audio);
        $this->is_voice     = isset($message->voice);
        $this->is_video     = isset($message->video);
        $this->is_document  = isset($message->document);
        $this->is_sticker   = isset($message->sticker);
        $this->is_raw_text  = isset($message->text);

        //Message Attributes
        $this->is_reply       = isset($message->reply_to_message);
        $this->is_reply_to_me = $this->is_reply ? ($message->reply_to_message->from->username == config('settings.bot_id')) : false;
        $this->chat_id        = $message->chat->id;
        $this->message_id     = $message->message_id;

        //Message Text
        if(isset($message->text)){
            $this->text = $message->text;
        }elseif(isset($message->caption)){
            $this->text = $message->caption;
        }elseif(isset($message->sticker)){
            $this->text = $message->sticker->emoji;
        }

        //Reply Message Text
        if($this->is_reply) {
            if (isset($message->reply_to_message->text)) {
                $this->replied_message_text = $message->reply_to_message->text;
            } elseif (isset($message->reply_to_message->caption)) {
                $this->replied_message_text = $message->reply_to_message->caption;
            }
        }
    }


}
