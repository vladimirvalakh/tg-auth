<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use App\Services\TelegramService;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use App\Models\City;

class TestController extends Controller
{
    public function sendMail(
        NotificationService $notificationService,
        TelegramService $telegramService,
        UserRepository $userRepository)
    {
//        $to_email = "sinclair.ubuntu@gmail.com";
//        $to_name = "Sinclair";
//        $from_email = "sinclair.ubuntu@gmail.com";
//        $from_name = "ЛидМаркетРФ";
//        $subject = "Новая заявка";
//
//
//        $email_message = "Тестовое сообщение с сайта";


       // $notificationService->sendEmail($to_email, $to_name, $from_email, $from_name, $subject, $email_message);



        //$userId = $userRepository->getUserIdByTelegramUsername('vladimir_valakh');

 //       $telegramService->sendToTelegramForUserId(127, $email_message);

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

        //$telegramService->sendToTelegramMessageWithButtonsForUserId(89, $email_message, $buttons);

//        $cities= City::all();
//
//        foreach ($cities as $city) {
//            if ($city->population) {
//                var_dump($city->population);
//                $city->population_number = (int)str_replace(" ", '', $city->population);
//                $city->save();
//                //var_dump($city->population_number);
//
//            }
//
//        }



        $cities =  DB::table('cities')
            ->where('subject_rf', 'Московская область')
            ->orderBy('population', 'DESC')
            ->pluck('city', 'id')
            ->toArray();

        array_unshift($cities, 'Выбрать всё');

        echo "<pre>";
        var_dump($cities);
        die('ok');
    }
}
