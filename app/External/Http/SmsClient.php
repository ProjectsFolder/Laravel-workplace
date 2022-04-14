<?php

namespace App\External\Http;

use App\Exceptions\SmsException;
use App\External\Config\ApiConfig;
use App\External\Interfaces\HttpClientInterface;
use App\External\Interfaces\SmsClientInterface;

class SmsClient implements SmsClientInterface
{
    protected $client;
    protected $config;
    protected $statuses = [
        -1 => 'Сообщение не найдено',
        100 => 'Запрос выполнен или сообщение находится в нашей очереди',
        101 => 'Сообщение передается оператору',
        102 => 'Сообщение отправлено (в пути)',
        103 => 'Сообщение доставлено',
        104 => 'Не может быть доставлено: время жизни истекло',
        105 => 'Не может быть доставлено: удалено оператором',
        106 => 'Не может быть доставлено: сбой в телефоне',
        107 => 'Не может быть доставлено: неизвестная причина',
        108 => 'Не может быть доставлено: отклонено',
        110 => 'Сообщение прочитано (для Viber, временно не работает)',
        150 => 'Не может быть доставлено: не найден маршрут на данный номер',
        200 => 'Неправильный api_id',
        201 => 'Не хватает средств на лицевом счету',
        202 => 'Неправильно указан номер телефона получателя, либо на него нет маршрута',
        203 => 'Нет текста сообщения',
        204 => 'Имя отправителя не согласовано с администрацией',
        205 => 'Сообщение слишком длинное (превышает 8 СМС)',
        206 => 'Будет превышен или уже превышен дневной лимит на отправку сообщений',
        207 => 'На этот номер нет маршрута для доставки сообщений',
        208 => 'Параметр time указан неправильно',
        209 => 'Вы добавили этот номер (или один из номеров) в стоп-лист',
        210 => 'Используется GET, где необходимо использовать POST',
        211 => 'Метод не найден',
        212 => 'Текст сообщения необходимо передать в кодировке UTF-8 (вы передали в другой кодировке)',
        213 => 'Указано более 100 номеров в списке получателей',
        220 => 'Сервис временно недоступен, попробуйте чуть позже',
        230 => 'Превышен общий лимит количества сообщений на этот номер в день',
        231 => 'Превышен лимит одинаковых сообщений на этот номер в минуту',
        232 => 'Превышен лимит одинаковых сообщений на этот номер в день',
        233 => 'Превышен лимит отправки повторных сообщений с кодом на этот номер за короткий промежуток времени ("защита от мошенников", можно отключить в разделе "Настройки")',
        300 => 'Неправильный token (возможно истек срок действия, либо ваш IP изменился)',
        301 => 'Неправильный api_id, либо логин/пароль',
        302 => 'Пользователь авторизован, но аккаунт не подтвержден (пользователь не ввел код, присланный в регистрационной смс)',
        303 => 'Код подтверждения неверен',
        304 => 'Отправлено слишком много кодов подтверждения. Пожалуйста, повторите запрос позднее',
        305 => 'Слишком много неверных вводов кода, повторите попытку позднее',
        500 => 'Ошибка на сервере. Повторите запрос.',
        901 => 'Callback: URL неверный (не начинается на http://)',
        902 => 'Callback: Обработчик не найден (возможно был удален ранее)'
    ];

    public function __construct(ApiConfig $config, HttpClientInterface $httpClient)
    {
        $this->config = $config;
        $this->client = $httpClient;
    }

    public function send(string $phone, string $message)
    {
        $response = $this->client->request('GET', "{$this->config->getBaseUrl()}/sms/send", [
            'query' => [
                'api_id' => $this->config->getApiKey(),
                'json' => 1,
                'to' => $phone,
                'msg' => $message,
            ],
        ]);

        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            throw new SmsException("sms.ru response code ({$response->getStatusCode()})");
        }

        $content = json_decode($response->getBody(), true);
        if (empty($content['status_code'])) {
            throw new SmsException("incorrect response sms.ru ({$response->getBody()})");
        }

        $statusCode = $content['status_code'];
        if ($statusCode !== 100) {
            $statusText = $this->statuses[$statusCode] ?? 'unknown';
            throw new SmsException("error sms.ru: $statusCode $statusText");
        }

        foreach ($content['sms'] ?? [] as $phone => $result) {
            $statusCode = $result['status_code'] ?? 0;
            if ($statusCode !== 100) {
                $statusText = $this->statuses[$statusCode] ?? 'unknown';
                throw new SmsException("error sms.ru by phone $phone: $statusCode $statusText");
            }
        }
    }
}
