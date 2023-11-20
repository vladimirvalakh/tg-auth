<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\TelegramService;
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
}
