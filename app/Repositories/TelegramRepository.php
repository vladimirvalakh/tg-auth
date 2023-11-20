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

    public function sendMessage($chatId, $message) {
        $telegramBotToken = env('TELEGRAM_BOT_TOKEN');

        $message = urlencode($message);

        try {
            file_get_contents("https://api.telegram.org/bot$telegramBotToken/sendMessage?chat_id=$chatId&parse_mode=html&text=" . $message);
        } catch (\Exception $e){
            var_dump($e->getMessage());
        }
    }


    public function sendMessageForUserId($userId, $message) {
        $telegramId = $this->userRepository->getUser($userId)->telegram_id;

        if (!$telegramId) {
            return;
        }
        $this->sendMessage($telegramId, $message);
    }

    public function sendMessageWithButtons($userId, $message, $buttons) {
        $telegramId = $this->userRepository->getUser($userId)->telegram_id;

        if (!$telegramId) {
            return;
        }
        $telegramBotToken = env('TELEGRAM_BOT_TOKEN');


        $inlineKeyboard = [
            "inline_keyboard" =>
                [ /* первый ряд кнопок - массив из наборов {подпись кнопки, даные для колбэка} */
                    [ /* первые две кнопки вызывают колбэк, а третья - открытие url-а */
                        [ "text" => "button 1",
                            "callback_data" => "data 1"
                        ],
                        [ "text" => "button 2",
                            "callback_data" => "data 2"
                        ],
                        [ "text" => "button 3",
                            "url" => "http://ya.ru"
                        ]
                    ]
                    /* здесь мог бы быть второй массив для второго ряда кнопок и так далее */
                ]
        ];

        $menuKeyboard = [
            "keyboard" =>
                [ /* первый ряд кнопок - массив из наборов {подпись кнопки, даные для колбэка} */
                    [ /* первые две кнопки вызывают колбэк, а третья - открытие url-а */
                        [ "text" => "button 1",
                            "callback_data" => "data 1"
                        ],
                        [ "text" => "button 2",
                            "callback_data" => "data 2"
                        ],
                        [ "text" => "button 3",
                            "url" => "http://ya.ru"
                        ]
                    ]
                    /* здесь мог бы быть второй массив для второго ряда кнопок и так далее */
                ]
        ];

        $telegramApiUrl = 'https://api.telegram.org/bot';

        $keyboardJson = json_encode($menuKeyboard); // перекодируем в json
        $url = $telegramApiUrl . $telegramBotToken . '/sendMessage?chat_id=' . $telegramId . '&text=' . urlencode($message) . '&parse_mode=HTML' . '&reply_markup=' . $keyboardJson;

        try {
            file_get_contents($url);
        } catch (\Exception $e){
            var_dump($e->getMessage());
        }
    }
}
