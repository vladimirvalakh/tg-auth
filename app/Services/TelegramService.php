<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Role;
use App\Models\Rent;
use App\Models\Site;
use App\Models\Order;
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
        $this->categoryRepository = $categoryRepository;
        $this->towRepository = $towRepository;
        $this->locationRepository = $locationRepository;
    }

    public function sendToTelegramForUserId($userId, $message)
    {
        $buttons = ["remove_keyboard" => true];

        $currentUser = $this->userRepository->getUser($userId);

        $role = $currentUser->role->slug;

//        if ($role == Role::OWNER_SLUG || $role == Role::ADMINISTRATOR_SLUG) {
//            $buttons = [
//                "keyboard" =>
//                    [
//                        [
//                            [ "text" => "Добавить сайт"],
//                            [ "text" => "Статистика"],
//                        ],
//                        [
//                            [ "text" => "Список добавленных сайтов"],
//                        ],
//                        [
//                            [ "text" => "Настройки профиля"],
//                            [ "text" => "Выплаты"]
//                        ]
//                    ]
//            ];
//        }

        if ($role == Role::MODERATOR_SLUG || $role == Role::ADMINISTRATOR_SLUG) {
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

        $text = $webhook["message"]["text"];
        $chatId = $webhook["message"]["chat"]["id"];
        $buttons = null;

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

            if ($text == "Статистика") {
                $message = "Вывод статистики пока не добавлен с систему \nПопробуйте повторить запрос позже\n";
            }

            if ($text == "Выплаты") {
                $message = "Информация по выплатам пока не добавлена с систему \nПопробуйте повторить запрос позже\n";
            }

            if ($text == "Заявки на модерацию") {
                $ordersForModeration = $this->orderRepository->getOrdersByStatus(Order::ON_MODERATION_STATUS);

                if (count($ordersForModeration)) {
                    $this->telegramRepository->sendMessage($chatId, "Список заявок на модерацию:\n\n");

                    foreach ($ordersForModeration as $number => $lead) {
                        $inlineMessage = $number + 1 . " - " . $lead->site_url . "\n";

                        $inlineButtons = [
                            "inline_keyboard" => [
                                [
                                    [ "text" => "Одобрить",
                                        "callback_data" => "approveOrder" . $lead->order_id
                                    ],
                                    [ "text" => "Отклонить",
                                        "callback_data" => "declineOrder" . $lead->order_id
                                    ],
                                ]
                            ]
                        ];

                        $this->telegramRepository->sendMessageWithButtonsByTelegramId($chatId, $inlineMessage, $inlineButtons);
                    }

                } else {
                    $message  = "Нет заявок на модерацию\n\n";
                }
            }

            if ($text == "Заявки на аренду") {
                $ordersForRent = $this->orderRepository->getOrdersByStatus(Order::ON_RENT_STATUS);

                if (count($ordersForRent)) {
                    $message = "Список добавленных сайтов:\n\n";

                    foreach ($leads as $number => $lead) {
                        $message .= $number + 1 . " - " . $lead->site_url . "\n";
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
