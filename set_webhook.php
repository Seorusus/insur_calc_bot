<?php

include_once 'settings.php';
include_once 'Telegram.php';

// Создаем экземпляр класса Telegram
$telegram = new Telegram(TG_TOKEN);

// URL вашего сервера, где расположен webhook.php
$webhookUrl = 'https://ваш-домен/путь-к-боту/webhook.php';

// Устанавливаем вебхук
$result = $telegram->setWebhook($webhookUrl);

// Выводим результат
echo '<pre>';
print_r($result);
echo '</pre>';

// Альтернативный способ проверки текущего статуса вебхука
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot' . TG_TOKEN . '/getWebhookInfo');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo '<pre>';
print_r(json_decode($response, true));
echo '</pre>';