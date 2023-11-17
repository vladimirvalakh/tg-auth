<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\UserRepository;

class TelegramRepository extends CustomRepository
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository,
    ) {
        $this->userRepository = $userRepository;
    }

    public function sendMessage($user_id, $message) {
        $telegramId = $this->userRepository->getUser($user_id)->telegram_id;

        if (!$telegramId) {
            return;
        }
        $telegramBotToken = env('TELEGRAM_BOT_TOKEN');

        $message = urlencode($message);

        try {
            file_get_contents("https://api.telegram.org/bot$telegramBotToken/sendMessage?chat_id=$telegramId&text=" . $message);
        } catch (\Exception $e){
            var_dump($e->getMessage());
        }
    }
}
