<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Mail;


class NotificationService
{
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
}
