<?php

include_once 'insur_calc_bot.php';

// Логирование для отладки
$logDir = "logs/";
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

file_put_contents('logs/webhook_access.log', date('Y-m-d H:i:s') . ' - Webhook accessed' . PHP_EOL, FILE_APPEND);

// Возвращаем успешный ответ Telegram
http_response_code(200);