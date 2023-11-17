<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use App\Repositories\UserRepository;

class TestController extends Controller
{
    public function sendMail(NotificationService $notificationService, UserRepository $userRepository)
    {
        $to_email = "sinclair.ubuntu@gmail.com";
        $to_name = "Sinclair";
        $from_email = "sinclair.ubuntu@gmail.com";
        $from_name = "ЛидМаркетРФ";
        $subject = "Новая заявка";


        $email_message = "Вы получили новую заявку по сайту <b>" . 'lalalala' . "</b>" . "<br />";
        $email_message .= "Город: <b>" . 'lalalala' . '</b>' . "<br />";
        $email_message .= "Телефон: <b>" . 'lalalala' . '</b>' . "<br />";
        $email_message .= "Источник: <b>" . 'lalalala' . '</b>' . "<br />";
        $email_message .= "Тип работ: <b>" . 'lalalala' . '</b>' . "<br />";


       // $notificationService->sendEmail($to_email, $to_name, $from_email, $from_name, $subject, $email_message);



        $userId = $userRepository->getUserIdByTelegramUsername('vladimir_valakh');

        //$notificationService->sendToTelegram(86, $email_message);
        var_dump($userId);
        die();
    }
}
