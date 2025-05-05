# Страховой Калькулятор Telegram Bot

Телеграм-бот для предоставления информации о страховых программах и расчета страховых выплат.

Основан на [TelegramBotPHP](https://github.com/Eleirbag89/TelegramBotPHP)

[![API](https://img.shields.io/badge/Telegram%20Bot%20API-April%2016%2C%202022-36ade1.svg)](https://core.telegram.org/bots/api)
![PHP](https://img.shields.io/badge/php-%3E%3D5.3-8892bf.svg)
![CURL](https://img.shields.io/badge/cURL-required-green.svg)

## Установка

1. Клонировать репозиторий
2. Настройка:
   - Создайте директории `data` и `logs` если их еще нет:
     ```
     mkdir -p data logs
     ```
   - Установите правильные права доступа:
     ```
     chmod -R 755 data logs
     ```

## Требования

* PHP >= 5.3
* Curl extension для PHP должен быть включен
* Telegram API ключ от [@BotFather](https://core.telegram.org/bots#botfather)

## Настройка бота

Бот можно запустить двумя способами:

### 1. Через Webhook

1. Отредактируйте файл `set_webhook.php`, укажите URL вашего сервера:
   ```php
   $webhookUrl = 'https://ваш-домен/путь-к-боту/webhook.php';
   ```

2. Загрузите файлы на ваш сервер, убедитесь что PHP и curl установлены.

3. Откройте URL `https://ваш-домен/путь-к-боту/set_webhook.php` в браузере для установки webhook.

4. Проверьте настройки webhook, открыв URL `https://api.telegram.org/bot{ВАШ_ТОКЕН}/getWebhookInfo` в браузере.

### 2. Через Long Polling

1. Запустите скрипт `longpolling.php` для запуска бота в режиме long polling:
   ```
   php longpolling.php
   ```

2. Бот будет работать до тех пор, пока скрипт активен.

## Структура проекта

- `insur_calc_bot.php` - основной файл бота
- `Telegram.php` - класс для работы с Telegram API
- `calculator/calculator_sep.php` - калькулятор программы СЕП
- `settings.php` - файл с настройками и константами
- `data/` - директория для хранения данных пользователей
- `logs/` - директория для логов
- `webhook.php` - обработчик webhook-запросов
- `set_webhook.php` - скрипт для установки webhook
- `delete_webhook.php` - скрипт для удаления webhook
- `longpolling.php` - скрипт для запуска бота в режиме long polling

## Функциональность

- Информация о различных страховых программах
- Калькулятор для программы СЕП
- Возможность получения консультации эксперта

## Отладка

Логи бота хранятся в директории `logs/`. Для решения проблем проверьте:

1. Логи в `logs/message.json` и `logs/webhook_access.log`
2. При использовании long polling проверьте `logs/updates.log` и `logs/longpolling.log`
3. Убедитесь, что бот имеет права на запись в директории `data/` и `logs/`

## Лицензия

Этот проект распространяется под лицензией MIT.
