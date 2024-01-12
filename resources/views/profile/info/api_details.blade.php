<?php
    $apiKey = env('API_KEY');
?>
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h3 class="container max-w-xl">
                    ИНФОРМАЦИЯ ПО НАСТРОЙКЕ API
                </h3>
                <hr>
                <p class="container">Для отправки уведомления о заявке для арендатора (через API) используется POST-запрос формата: <br />
                <br />
                    Method: <code>POST</code> / HTTP/1.1<br />
                    Host: <code>{{config('app.url')}}/api/v1/order/send</code><br />
                    Content-Type: <code>application/x-www-form-urlencoded</code><br />
                    Authorization Token: <code>Bearer <em>token</em></code> (секретный токен доступа можно скопировать <a href="{{route('profile.edit')}}" target="_blank">на своей странице профиля</a> в поле "API ключ")<br /><br>
                    BODY:<br />
                    обязательные параметры:<br />
                    <code>site_id</code> - код сайта (посмотреть можно в списке сайтов по кнопке "Детали")<br />
                    <code>phone</code> - номер телефона<br />
                    <code>tow_id</code> - код типа работ<br />
                    <code>source</code> - телефон источника или иной идентификатор<br />
                    <br />
                    необязательный параметр:<br />
                    <code>info</code> - текст описания заявки<br />
                </p>
                <p class="container">В результате успешного запроса заявка будет добавлена в таблицу статистики и на емейл арендатора будет отправлено письмо с уведомлением.<br />
            </div>
        </div>
    </div>
</x-app-layout>
