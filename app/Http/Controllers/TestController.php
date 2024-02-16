<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use App\Services\TelegramService;
use App\Repositories\UserRepository;
use App\Repositories\SiteRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\City;
use App\Models\Rent;

class TestController extends Controller
{
    public function sendMail(
        NotificationService $notificationService,
        TelegramService $telegramService,
        UserRepository $userRepository,
        SiteRepository $siteRepository,
    )
    {
        $to_email = "sinclair.ubuntu@gmail.com";
        $to_name = "Sinclair";
        $from_email = "sinclair.ubuntu@gmail.com";
        $from_name = "ЛидМаркетРФ";
        $subject = "Новая заявка";


        $email_message = "Тестовое сообщение с сайта";


        //$notificationService->sendEmail($to_email, $to_name, $from_email, $from_name, $subject, $email_message);



//        $userId = $userRepository->getUserIdByTelegramUsername('vladimir_valakh');
//
//
//
//
//
//
//        $telegramService->sendToTelegramForUserId($userId, $email_message);

//        $buttons = [
//            'inline_keyboard' => [
//                  [
//                      'text' => 'Button 1',
//                      'callback_data' => 'test_2',
//                  ],
//                [
//                    'text' => 'Button 2',
//                    'callback_data' => 'test_2',
//                ]
//            ],
//        ];

//        $buttons = [
//            "keyboard" =>
//                [ /* первый ряд кнопок - массив из наборов {подпись кнопки, даные для колбэка} */
//                    [ /* первые две кнопки вызывают колбэк, а третья - открытие url-а */
//                        [ "text" => "Добавить сайт", "callback_data" => "add_site"],
//                        [ "text" => "Статистика 2",
//                            "callback_data" => "statistics"
//                        ],
//                    ],
//                    [
//                        [ "text" => "Список добавленных сайтов",
//                            "callback_data" => "list_added_sites"
//                        ],
//                    ],
//                    [
//                        [ "text" => "Настройки профиля", "callback_data" => "/profile"],
//                        [ "text" => "Выплаты", "callback_data" => "payments"]
//                    ]
//                ]
//        ];

        //$telegramService->sendToTelegramMessageWithButtonsForUserId(100, $email_message, []);

        $rents= Rent::all();

        foreach ($rents as $rent) {
            $rent['start_rent_date'] = Carbon::today();
            $rent['finish_rent_date'] = Carbon::parse('Now +30 days');
                $rent->save();
                //var_dump($city->population_number);



        }



//        $cities =  DB::table('cities')
//            ->where('subject_rf', 'Московская область')
//            ->orderBy('population', 'DESC')
//            ->pluck('city', 'id')
//            ->toArray();
//
//        array_unshift($cities, 'Выбрать всё');
//
//        echo "<pre>";
//        var_dump($cities);
        die('ok');
    }
}
