<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Repositories\TelegramRepository;
use App\Repositories\UserRepository;


class TelegramService
{
    private TelegramRepository $telegramRepository;
    private UserRepository $userRepository;

    public function __construct(
        TelegramRepository $telegramRepository,
        UserRepository $userRepository,
    ) {
        $this->telegramRepository = $telegramRepository;
        $this->userRepository = $userRepository;
    }

    public function sendToTelegramForUserId($userId, $message)
    {
        $this->telegramRepository->sendMessageForUserId($userId, $message);
    }

    public function sendToTelegramMessageWithButtonsForUserId($user_id, $message, $buttons)
    {
        $this->telegramRepository->sendMessageWithButtons($user_id, $message, $buttons);
    }

    public function getTelegramWebhook($webhook)
    {
        Log::debug('NotificationService/getTelegramWebhook', [
            'webhook' => $webhook,
        ]);

        $text = $webhook["message"]["text"];
        $chatId = $webhook["message"]["chat"]["id"];


//        if (!empty($webhook["message"] && !empty($webhook["message"]["text"]))) {
//            $text = $webhook["message"]["text"];
//            $chatId = $webhook["message"]["chat"]["id"];
//        } elseif (!empty($webhook["text"])) {
//            $text = $webhook["text"];
//            $chatId = $webhook["chat"]["id"];
//        } else {
//            Log::debug('NotificationService/getTelegramWebhook', [
//                'webhook' => $webhook,
//                'error' => 'webhook without text',
//            ]);
//
//            return;
//        }

        Log::debug('NotificationService/getTelegramWebhook', [
            'text' => $text,
            'chatId' => $chatId,
        ]);

        if ($text) {
            if ($text == "/api") {
                $message = "Информация по настройке API: \n\n";
                $message .= "Для отправки уведомления о заявке для арендатора (через API) используется POST-запрос формата:\n";
                $message .= "Method: <code>POST</code> / HTTP/1.1\n";
                $message .= "Host: <code>" . config('app.url') . "/api/v1/order/send</code>\n";
                $message .= "Content-Type: <code>application/x-www-form-urlencoded</code>\n\n";
                $message .= "BODY:\n\n";
                $message .= "обязательные параметры:\n";
                $message .= "<code>site_id</code> - код сайта (посмотреть можно в списке сайтов по кнопке 'Детали')\n";
                $message .= "<code>phone</code> - номер телефона\n";
                $message .= "<code>tow_id</code> - код типа работ\n";
                $message .= "<code>source</code> - телефон источника или иной идентификатор\n\n";
                $message .= "необязательный параметр:\n";
                $message .= "<code>info</code> - текст описания заявки\n\n";
                $message .= "В результате успешного запроса заявка будет добавлена в таблицу статистики, в телеграмм и на емейл арендатора будет отправлено письмо с уведомлением.\n";
            }

            if ($text == "/profile") {
                $currentUser = $this->userRepository->getUserByTelegramId($chatId);
                $message = "Данные о пользователе: \n\n";
                $message .= "<code>ID пользователя в системе:</code> " . $currentUser->id . "\n";
                $message .= "<code>Имя:</code> " . $currentUser->name . "\n";
                $message .= "<code>Ф.И.O:</code> " . $currentUser->full_name . "\n";
                $message .= "<code>Телефон:</code> " . $currentUser->phone . "\n";
                $message .= "<code>Роль:</code> " . $currentUser->role->name . "\n";
            }

            if ($text == "/orders") {
                $currentUser = $this->userRepository->getUserByTelegramId($chatId);
                $role = $currentUser->role->name;
                if ($role == Role::ADMINISTRATOR_SLUG) {
                    $message = "Для пользователей с ролью '". $role ."' заявок нет.\n";
                }

                $message = "Для просмотра заявок на сайте <a href='https://test.lead-mart.ru/orders'>перейдите по ссылке</a>.\n";
            }

            if (isset($message) && $message != '') {
                $this->telegramRepository->sendMessage($chatId, $message);
            }
        }
    }
}
