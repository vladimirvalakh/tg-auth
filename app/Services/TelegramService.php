<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Repositories\TelegramRepository;


class TelegramService
{
    private TelegramRepository $telegramRepository;

    public function __construct(
        TelegramRepository $telegramRepository,
    ) {
        $this->telegramRepository = $telegramRepository;
    }

    public function sendToTelegramForUserId($userId, $message)
    {
        $message = str_replace("<br />", "\n", $message);
        $this->telegramRepository->sendMessageForUserId($userId, $message);
    }

    public function sendToTelegramMessageWithButtonsForUserId($user_id, $message, $buttons)
    {
        $message = str_replace("<br />", "\n", $message);
        $this->telegramRepository->sendMessageWithButtons($user_id, $message, $buttons);
    }

    public function getTelegramWebhook($webhook)
    {
        $text = $webhook["message"]["text"];
        $chatId = $webhook["message"]["chat"]["id"];

        Log::debug('NotificationService/getTelegramWebhook', [
            'text' => $text,
            'webhook' => $webhook,
        ]);

        if ($text) {
            if ($text == "/orders") {
                $message = "Orders: ";
                $this->telegramRepository->sendMessage($chatId, $message);

            } else {
                $message = "По запросу \"<b>" . $text . "</b>\" ничего не найдено.";
                $this->telegramRepository->sendMessage($chatId, $message);
            }
        }
    }
}
