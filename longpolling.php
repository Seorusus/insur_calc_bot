<?php

include_once 'settings.php';
include_once 'Telegram.php';

// Сначала удалим вебхук, если он был установлен
$telegram = new Telegram(TG_TOKEN);
$telegram->deleteWebhook();

// Создаем лог-директорию если её нет
$logDir = "logs/";
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

// Создаем директорию для данных, если её нет
$dataDir = "data/";
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

echo "Starting longpolling...\n";
file_put_contents('logs/longpolling.log', date('Y-m-d H:i:s') . ' - Longpolling started' . PHP_EOL, FILE_APPEND);

// Устанавливаем смещение для getUpdates, начиная с 0
$offset = 0;

// Бесконечный цикл запросов обновлений
while (true) {
    // Получаем обновления от Telegram, с учетом нашего смещения
    $updates = $telegram->getUpdates($offset, 100, 20);
    
    // Если есть обновления, обрабатываем их
    if (isset($updates['result']) && !empty($updates['result'])) {
        foreach ($updates['result'] as $update) {
            // Обновляем смещение для следующего запроса
            $offset = $update['update_id'] + 1;
            
            // Логируем полученное обновление
            file_put_contents(
                'logs/updates.log', 
                date('Y-m-d H:i:s') . ' - Update ID: ' . $update['update_id'] . PHP_EOL, 
                FILE_APPEND
            );
            
            // Устанавливаем данные для обработки
            $telegram->setData($update);
            
            // Обрабатываем обновление, включая наш insur_calc_bot.php
            include 'insur_calc_bot.php';
        }
    }
    
    // Задержка для уменьшения нагрузки на API
    sleep(1);
}