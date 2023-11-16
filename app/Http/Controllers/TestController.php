<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;

class TestController extends Controller
{
    public function sendMail(NotificationService $notificationService)
    {
//        $to_email = "sinclair.ubuntu@gmail.com";
//        $to_name = "Sinclair";
//        $from_email = "sinclair.ubuntu@gmail.com";
//        $from_name = "ЛидМаркетРФ";
//        $subject = "Новая заявка";
//        $email_message = "Вы получили новую заявку";
//
//
//        $notificationService->sendEmail($to_email, $to_name, $from_email, $from_name, $subject, $email_message);
        $notificationService->sendToTelegram(86, "Hello!!!\nКак сам?");
        var_dump('ok');
        die();
    }
}
