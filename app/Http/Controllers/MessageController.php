<?php

namespace App\Http\Controllers;

use App\Message;
use App\Repositories\Train;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function hook(Request $request)
    {
        $data = $request->getContent();
        $message = new Message($data);

        //Train AI
        new Train($message);

        //Predict

        //Send Response
    }
}
