<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Telegram\Bot\Api;


class TelegramController extends Controller
{

    private TelegramService $telegramService;

    public function __construct(
        TelegramService $telegramService
    )
    {
        $this->telegramService = $telegramService;
    }

    public function webhook()
    {
        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $webhook = $telegram->getWebhookUpdate();

        $this->telegramService->getTelegramWebhook($webhook);
    }

    public function sendMessageModal(User $user)
    {
        return ['user' => $user];
    }

    public function sendMessage(Request $request)
    {
        $userId = (int)$request->get('user_id');
        $message = $request->get('message');

        $this->telegramService->sendToTelegramForUserId($userId, $message);

        return Redirect::route('profiles')->with('success','Сообщение в Telegram отправлено.');
    }
}
