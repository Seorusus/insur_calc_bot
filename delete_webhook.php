<?php

include_once 'settings.php';
include_once 'Telegram.php';

// Создаем экземпляр класса Telegram
$telegram = new Telegram(TG_TOKEN);

// Удаляем вебхук
$result = $telegram->deleteWebhook();

// Выводим результат
echo '<pre>';
print_r($result);
echo '</pre>';