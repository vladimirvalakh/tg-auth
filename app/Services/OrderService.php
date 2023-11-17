<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use App\Repositories\TowRepository;
use App\Repositories\SiteRepository;
use App\Services\NotificationService;

class OrderService
{

    private OrderRepository $orderRepository;
    private UserRepository $userRepository;
    private TowRepository $towRepository;
    private SiteRepository $siteRepository;
    private NotificationService $notificationService;

    public function __construct(
        OrderRepository $orderRepository,
        UserRepository $userRepository,
        TowRepository $towRepository,
        SiteRepository $siteRepository,
        NotificationService $notificationService
    ) {
        $this->orderRepository = $orderRepository;
        $this->notificationService = $notificationService;
        $this->userRepository = $userRepository;
        $this->towRepository = $towRepository;
        $this->siteRepository = $siteRepository;
    }

    public function sendOrderToArendator(array $data)
    {
        $this->orderRepository->store($data);

        if (!$data['user_id']) {
            return "User is missing";
        };

        $arendator = $this->userRepository->getUser($data['user_id']);

        if (!$arendator) {
            return "User not found";
        };

        $site = $this->siteRepository->getSite($data['site_id']);

        if (!$site) {
            return "Site not found";
        };

        $emails = $site->rent->emails;

        if (!$emails) {
            return "Email not found";
        };

        $tow = $this->towRepository->getTow($data['tow_id']);

        if (!$tow) {
            return "Tow not found";
        };

        $to_email = $emails;
        $to_name = $arendator->name;
        $from_email = "sinclair.ubuntu@gmail.com";
        $from_name = "Лидмаркет.рф";
        $subject = "Новая заявка";
        $email_message = "Вы получили новую заявку по сайту <b>" . $site->url . "</b><br />";
        $email_message .= "Город: <b>" . $site->getCityName() . '</b><br />';
        $email_message .= "Телефон: <b>" . $data['phone'] . '</b><br />';
        $email_message .= "Источник: <b>" . $data['source'] . '</b><br />';
        $email_message .= "Тип работ: <b>" . $tow->tow . '</b><br />';

        if (!empty($data['info'])) {
            $email_message .= "<p><b>" . $data['info'] . '</b></p>';
        }

        $this->notificationService->sendEmail($to_email, $to_name, $from_email, $from_name, $subject, $email_message);
        $this->notificationService->sendToTelegram($data['user_id'], $email_message);

        //duplicate for me
        $myUserId = $this->userRepository->getUserIdByTelegramUsername('vladimir_valakh');
        $this->notificationService->sendEmail("sinclair.ubuntu@gmail.com", "Разработчик", $from_email, $from_name, "Новая заявка (тестовая копия для разработчика)", $email_message);
        $this->notificationService->sendToTelegram($myUserId, $email_message);

        return "ok";
    }
}
