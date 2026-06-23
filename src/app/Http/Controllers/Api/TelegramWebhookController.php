<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TransactionParserService;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request, TransactionParserService $parser)
    {
        $chatId = data_get($request, 'message.chat.id');
        $text = trim(data_get($request, 'message.text', ''));

        $user = User::where('telegram_chat_id', $chatId)->first();

        if (! $user) {
            return response()->json(['ok' => true]);
        }

        $parsed = $parser->parse($text, $user->id);

        if ($parsed) {
            $parser->save($parsed, $user->id);
        }

        return response()->json(['ok' => true]);
    }
}
