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

    public function getUpdates()
    {
        $telegram = new \Telegram(env('TELEGRAM_BOT_TOKEN'));
        $updates = $telegram->getUpdates();


        for ($i = 0; $i < $telegram->UpdateCount(); $i++) {
            $telegram->serveUpdate($i);
            $text = $telegram->Text();
            $chatId = $telegram->ChatID();

            $webhook = [
                ["message"]["text"] => $text,
                ["message"]["chat"]["id"] => $chatId
            ];

            $this->telegramService->getTelegramWebhook($webhook);

//
//            var_dump($text);
//
//            if ($text == '/aga') {
//                // Create option for the custom keyboard. Array of array string
//                $keyboard_option = [['A', 'B']];
//                $inline_keyboard_option = [
//                  [
//                      'text' => 'Button 1',
//                      'callback_data' => 'test_2'
//                  ],
//                    [
//                        'text' => 'Button 2',
//                        'callback_data' => 'test_2',
//                    ]
//                ];
//
//                $reply = '<b>Some text</b><b>And text</b>';
//
//                // Get the keyboard
//                //$keyb = $telegram->buildKeyBoard($option, false, true);
//                //$keyb = $telegram->buildInlineKeyBoard($inline_keyboard_option);
//
//                $buttons = [
//                    'inline_keyboard' => [
//                          [
//                              'text' => 'Button 1',
//                              'callback_data' => 'test_2',
//                          ],
//                        [
//                            'text' => 'Button 2',
//                            'callback_data' => 'test_2',
//                        ]
//                    ],
//                ];
//
//                $content = [
//                    'chat_id' => $chatId,
//                    'reply_markup' => json_encode($buttons),
//                    'parse_mode' => 'html',
//                    'text' => $reply
//                ];
//                try {
//                    $telegram->sendMessage($content);
//                } catch (\Exception $exception) {
//                    var_dump($exception->getMessage());
//                }

//            }
        }
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
