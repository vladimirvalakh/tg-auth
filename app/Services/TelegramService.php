<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Role;
use App\Models\Rent;
use App\Models\Site;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Repositories\TelegramRepository;
use App\Repositories\SiteRepository;
use App\Repositories\TowRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\LocationRepository;
use App\Repositories\UserRepository;


class TelegramService
{
    private TelegramRepository $telegramRepository;
    private UserRepository $userRepository;
    private OrderRepository $orderRepository;
    private SiteRepository $siteRepository;
    private CategoryRepository $categoryRepository;
    private TowRepository $towRepository;
    private LocationRepository $locationRepository;

    public function __construct(
        TelegramRepository $telegramRepository,
        UserRepository $userRepository,
        SiteRepository $siteRepository,
        OrderRepository $orderRepository,
        CategoryRepository $categoryRepository,
        TowRepository $towRepository,
        LocationRepository $locationRepository,
    ) {
        $this->telegramRepository = $telegramRepository;
        $this->userRepository = $userRepository;
        $this->siteRepository = $siteRepository;
        $this->orderRepository = $orderRepository;
        $this->orderRepository = $orderRepository;
        $this->categoryRepository = $categoryRepository;
        $this->towRepository = $towRepository;
        $this->locationRepository = $locationRepository;
    }

    public function sendToTelegramForUserId($userId, $message)
    {
        $buttons = ["remove_keyboard" => true];

        $currentUser = $this->userRepository->getUser($userId);

        $role = $currentUser->role->slug;

        if ($role == Role::OWNER_SLUG) {
            $buttons = [
                "keyboard" =>
                    [
                        [
                            [ "text" => "Добавить сайт"],
                            [ "text" => "Статистика"],
                        ],
                        [
                            [ "text" => "Список добавленных сайтов"],
                        ],
                        [
                            [ "text" => "Настройки профиля"],
                            [ "text" => "Выплаты"]
                        ]
                    ]
            ];
        }

        if ($role == Role::MODERATOR_SLUG) {
            $buttons = [
                "keyboard" =>
                    [
                        [
                            [ "text" => "Заявки на модерацию"],
                            [ "text" => "Заявки на аренду"],
                        ],
                    ]
            ];
        }

        if ($role == Role::MANAGER_SLUG || $role == Role::ADMINISTRATOR_SLUG) {
            $buttons = [
                "keyboard" =>
                    [
                        [
                            [ "text" => "Список сайтов для аренды"],
                        ],
                        [
                            [ "text" => "Настройки профиля"],
                            [ "text" => "Реквизиты"],
                        ],
                    ]
            ];
        }

        $this->sendToTelegramMessageWithButtonsForUserId($userId, $message, $buttons);
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

        //is callback query
        if (!isset($webhook["message"])) {
            Log::debug('NotificationService/getTelegramWebhook', [
                'exception' => "Message is empty",
                'webhook' => $webhook,
            ]);

            if (isset($webhook["callback_query"])) {
                Log::debug('NotificationService/getTelegramWebhook', [
                    'callback_data' => $webhook["callback_query"]["data"],
                ]);
            }

            if (empty($webhook["callback_query"]["data"])) {
                return;
            }

            $callbackData = $webhook["callback_query"]["data"];
            $chatId = $webhook["callback_query"]["from"]["id"];

            //approveOrder_
            if (str_contains($callbackData, "approveOrder_")) {
                $data = explode("_", $callbackData);
                $orderId = $data[1];
                $this->orderRepository->approve($orderId);
                $this->telegramRepository->sendMessage($chatId, "Заявка одобрена");
                return;
            }

            //declineOrder_
            if (str_contains($callbackData, "declineOrder_")) {
                $data = explode("_", $callbackData);
                $orderId = $data[1];
                $this->orderRepository->decline($orderId);
                $this->telegramRepository->sendMessage($chatId, "Заявка отклонена");
                return;
            }

            //approveRent_
            if (str_contains($callbackData, "approveRent_")) {
                $data = explode("_", $callbackData);
                $rentId = $data[1];
                $this->orderRepository->approveRent($rentId);
                $this->telegramRepository->sendMessage($chatId, "Заявка одобрена");
                return;
            }

            //declineRent_
            if (str_contains($callbackData, "declineRent_")) {
                $data = explode("_", $callbackData);
                $rentId = $data[1];
                $this->orderRepository->declineRent($rentId);
                $this->telegramRepository->sendMessage($chatId, "Заявка отклонена");
                return;
            }

            //requestUpdateRent_
            if (str_contains($callbackData, "requestUpdateRent_")) {
                $data = explode("_", $callbackData);
                $orderId = $data[1];

                $text = "Опишите, пожалуйста, необходимые доработки по заявке № " . $orderId . " в формате:\n";
                $text .= "Необходимые доработки по заявке " . $orderId . ": <em>любой текст</em>\n\n";
                $text .= "Например:\n\n";
                $text .= "Необходимые доработки по заявке 340: Отсутствует email, необходимо увеличить кол-во заявок в месяц (больше 5)\n\n";

                $this->telegramRepository->sendMessage($chatId, $text);
                return;
            }

            return;
        }

        $text = $webhook["message"]["text"];
        $chatId = $webhook["message"]["chat"]["id"];
        $buttons = null;
        $currentUser = $this->userRepository->getUserByTelegramId($chatId);

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

            if ($text == "/profile" || $text == "Настройки профиля") {
                $currentUser = $this->userRepository->getUserByTelegramId($chatId);
                $message = "Данные о пользователе: \n\n";
                $message .= "<code>ID пользователя в системе:</code> " . $currentUser->id . "\n";
                $message .= "<code>Имя:</code> " . $currentUser->name . "\n";
                $message .= "<code>Ф.И.O:</code> " . $currentUser->full_name . "\n";
                $message .= "<code>Телефон:</code> " . $currentUser->phone . "\n";
                $message .= "<code>Роль:</code> " . $currentUser->role->name . "\n";
            }

            if ($text == "Добавить сайт") {
                $message = "Укажите данные для нового сайта в следующем формате (через запятую):\n\n";

                $message .= "Добавление сайта:\n";
                $message .= "Город, Категория, Адрес сайта, Номер телефона заявки, Дополнительная информация,\n Количество заявок за последний месяц (должно быть не менее 5 заявок)\n\n";

                $message .= "Например:\n\n";

                $message .= "Добавление сайта:\n";
                $message .= "Москва, Клининговые услуги, http://cleaning-services.ru, 8888 888 888, Перезвоните мне, 10\n";
            }

            if (str_contains($text, "Добавление сайта:")) {
                if (is_int($this->parseForAddSite($text)) && $this->parseForAddSite($text) > 0) {
                    $message = "Сайт успешно добавлен";
                }

                if (is_string($this->parseForAddSite($text))) {
                    $message = $this->parseForAddSite($text);
                }
            }

            if (str_contains($text, "Необходимые доработки по заявке ")) {
                $data = explode(":", $text);
                $headerInfo = array_shift($data);
                $textMessage =  trim(implode(":", $data));
                $rentId  = preg_replace("/[^0-9]/", "", $headerInfo);

                Log::debug('TelegramService/Необходимые доработки по заявке', [
                    'rentId' => $rentId,
                    'message' => $textMessage
                ]);

                $orderId = $this->orderRepository->getOrderIdByRentId($rentId);

                if (!$orderId) {
                    $this->telegramRepository->sendMessage($chatId, "Не найдена актуальная заявка по брони № " . $rentId);
                    return;
                }

                Log::debug('TelegramService/Необходимые доработки по заявке 2', [
                    'rentId' => $rentId,
                    'message' => $textMessage,
                    'orderId' => $orderId
                ]);

                $this->orderRepository->decline($orderId, "", $textMessage);
                $this->telegramRepository->sendMessage($chatId, "Заявка № ". $rentId ." отклонена, сообщение о необходимых доработках отправлено");
                return;
            }

            if ($text == "Список добавленных сайтов") {

                $leads = $this->siteRepository->getAddedSitesOfUserId(Auth::id());

                if (count($leads)) {
                    $message = "Список добавленных сайтов:\n\n";

                    foreach ($leads as $number => $lead) {
                        $message .= $number + 1 . " - " . $lead->url . "\n";
                    }
                } else {
                    $message  = "Вы ещё не добавили ни одного сайта\n\n";
                }
            }

            if ($text == "Список сайтов для аренды") {

                $leads = $this->siteRepository->getRentSitesForManagerByUserId($currentUser->id);

                if (count($leads)) {
                    $inlineMessage = "Список сайтов в аренде:\n\n";

                    foreach ($leads as $number => $lead) {
                        $inlineMessage .= $number + 1 . " - " . $lead->site_url . "\n";
                        $inlineMessage .= 'Дата окончания аренды' . " - " . Carbon::parse($lead->finish_rent_date)->isoformat("D MMMM Y") . "\n\n";
                    }

                    $this->telegramRepository->sendMessage($chatId, $inlineMessage);

                } else {
                    $message  = "Нет сайтов в аренде\n\n";
                }
            }

            if ($text == "Статистика") {
                $message = "Вывод статистики пока не добавлен с систему \nПопробуйте повторить запрос позже\n";
            }

            if ($text == "Реквизиты") {
                if (!$currentUser->bank_cards) {
                    $this->telegramRepository->sendMessage($chatId, "Нет информации по банковским реквизитам\n\n");
                    return;
                }

                $bankCards = json_decode($currentUser->bank_cards, true);;

                if (count($bankCards)) {
                    $inlineMessage = "Банковские реквизиты:\n\n";

                    foreach ($bankCards as $number => $value) {
                        $inlineMessage .= "Банк - " . $value['bank'] . "\n";
                        $inlineMessage .= 'Номер карты' . " - " . $value['card_number'] . "\n\n";
                    }

                    $this->telegramRepository->sendMessage($chatId, $inlineMessage);

                } else {
                    $message  = "Нет информации по реквизитам\n\n";
                }
            }

            if ($text == "Выплаты") {
                $message = "Информация по выплатам пока не добавлена с систему \nПопробуйте повторить запрос позже\n";
            }

            if ($text == "Заявки на модерацию") {
                $sitesForModeration = $this->siteRepository->getSitesByRentStatus(Rent::ON_MODERATION_STATUS);

                if (count($sitesForModeration)) {
                    $this->telegramRepository->sendMessage($chatId, "Список заявок на модерацию:\n\n");

                    foreach ($sitesForModeration as $number => $site) {
                        $inlineMessage = $number + 1 . " - " . $site->site_url . "\n";

                        if (!empty($site->rent_phone)) {
                            $inlineMessage .= "Номер телефона - " . $site->rent_phone . "\n";
                        }

                        if (!empty($site->last_month_orders_count)) {
                            $inlineMessage .= "Заявок за последний месяц - " . $site->last_month_orders_count . "\n";
                        }

                        $inlineButtons = [
                            "inline_keyboard" => [
                                [
                                    [ "text" => "Одобрить",
                                        "callback_data" => "approveRent_" . $site->rent_id
                                    ],
                                    [ "text" => "Отклонить",
                                        "callback_data" => "declineRent_" . $site->rent_id
                                    ],
                                ],
                                [
                                    [ "text" => "Отправить на доработку",
                                        "callback_data" => "requestUpdateRent_" . $site->rent_id
                                    ],
                                ]
                            ]
                        ];

                        $this->telegramRepository->sendMessageWithButtonsByTelegramId($chatId, $inlineMessage, $inlineButtons);
                        $this->telegramRepository->sendMessage($chatId, "------------------------------------\n\n");
                    }

                } else {
                    $message  = "Нет заявок на модерацию\n\n";
                }
            }

            if ($text == "Заявки на аренду") {
                $ordersForModeration = $this->orderRepository->getOrdersByStatus(Order::ON_MODERATION_STATUS);

                if (count($ordersForModeration)) {
                    $this->telegramRepository->sendMessage($chatId, "Список заявок на аренду:\n\n");

                    foreach ($ordersForModeration as $number => $lead) {
                        $inlineMessage = $number + 1 . " - " . $lead->site_url . "\n";

                        if (!empty($lead->order_phone)) {
                            $inlineMessage .= "Номер телефона - " . $lead->order_phone . "\n";
                        }

                        if (!empty($lead->order_source)) {
                            $inlineMessage .= "Источник - " . $lead->order_source . "\n";
                        }

                        if (!empty($lead->order_info)) {
                            $inlineMessage .= "Дополнительная информация - " . $lead->order_info . "\n";
                        }


                        $inlineButtons = [
                            "inline_keyboard" => [
                                [
                                    [ "text" => "Одобрить",
                                        "callback_data" => "approveOrder_" . $lead->order_id
                                    ],
                                    [ "text" => "Отклонить",
                                        "callback_data" => "declineOrder_" . $lead->order_id
                                    ],
                                ]
                            ]
                        ];

                        $this->telegramRepository->sendMessageWithButtonsByTelegramId($chatId, $inlineMessage, $inlineButtons);
                        $this->telegramRepository->sendMessage($chatId, "------------------------------------\n\n");
                    }

                } else {
                    $message  = "Нет заявок на аренду\n\n";
                }
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
                if ($buttons) {
                    $this->telegramRepository->sendMessageWithButtonsByTelegramId($chatId, $message, $buttons);
                } else {
                    $this->telegramRepository->sendMessage($chatId, $message);
                }
            }
        }
    }

    private function parseForAddSite(string $text)
    {
        $data = explode(",", $text);
        $site = [];

        if (count($data) != 6) {
            return "Неверный формат введённых данных. Проверьте количество и порядок введённых данных";
        }

        foreach ($data as $key => $value) {
            if ($key == 0) {
                $header = explode(":", $value);
                $cityName = trim($header[1]);
                $cityId = $this->locationRepository->getCityIdByName($cityName);

                if (!$cityId) {
                    return "Такого города нет в системе. Проверьте, пожалуйста, введённые данные";
                }

                $site['city_id'] = $cityId;
            }
            if ($key == 1) {
                $categoryName = trim($value);
                $categoryId = $this->categoryRepository->getCategoryIdByName($categoryName);

                if (!$categoryId) {
                    return "Такой категории нет в системе. Проверьте, пожалуйста, введённые данные";
                }

                $site['cat_id'] = $categoryId;
            }
            if ($key == 2) {
                $site['url'] = trim($value);
            }
            if ($key == 3) {
                $site['phone'] = trim($value);
            }
            if ($key == 4) {
                $site['info'] = trim($value);
            }
            if ($key == 5) {
                $site['last_month_orders_count'] = trim($value);
            }
        }

        if (empty($site)) {
            return 0;
        } else {
            if ($site['last_month_orders_count'] < 5) {
                return "К сожалению, ваш канал будет очень сложно сдать в аренду. Попробуйте увеличить количество заявок и оставить заявку заново.";
            }

            $searchRecord = Site::where('cat_id', $site['cat_id'])
                ->where('url', $site['url'])
                ->where('city_id', $site['city_id'])
                ->first();

            if (!$searchRecord) {
                $siteModel = Site::create([
                    'cat_id' => $site['cat_id'],
                    'url' =>  $site['url'],
                    'city_id' => $site['city_id'],
                    'comment' => $site['info'],
                    'last_month_orders_count' => $site['last_month_orders_count'],
                    'phone1' => $site['phone'],
                ]);

                Rent::create([
                    'site_id' => $siteModel->id,
                    'status' => Rent::IN_SEARCH_STATUS
                ]);

                return $siteModel->id;
            }
        }
    }
}
