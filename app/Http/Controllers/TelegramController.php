<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController extends Controller
{
    public function send(Request $request)
    {
       Telegram::sendMessage([
            'chat_id' => '-4890343547',
            'text' => 'Hello, this is a test message from Laravel Telegram Bot API',
        ]);
        return response()->json(['message' => 'Message sent successfully']);
    }
}
