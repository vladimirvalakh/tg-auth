<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Repositories\UserRepository;


class NotificationService
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository,
    ) {
        $this->userRepository = $userRepository;
    }

    public function sendEmail($to_email, $to_name, $from_email, $from_name, $subject, $email_message)
    {
        $data = [
            "name" => $to_name,
            "body" => $email_message
        ];

        Mail::send('emails.mail', $data, function($message) use (
            $to_name,
            $to_email,
            $from_email,
            $from_name,
            $subject
        ) {
            $message->to($to_email, $to_name)
            ->subject($subject);
            $message->from($from_email, $from_name);
        });

    }

    public function sendToTelegram($user_id, $message)
    {
        $telegramId = $this->userRepository->getUser($user_id)->telegram_id;
        $telegramBotToken = env('TELEGRAM_BOT_TOKEN');

        $message = urlencode($message);

        try {
            file_get_contents("https://api.telegram.org/bot$telegramBotToken/sendMessage?chat_id=$telegramId&text=" . $message);
        } catch (\Exception $e){
            var_dump($e->getMessage());
        }
    }
}
