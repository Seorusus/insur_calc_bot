<?php

include_once 'settings.php';

// Instances the class
$telegram = new Telegram(TG_TOKEN);

// Take text and chat_id from the message
$text = $telegram->Text();
$chat_id = $telegram->ChatID();
$result = $telegram->getData();
$callbackQuery = $telegram->Callback_Query();
$callbackData = $telegram->Callback_Data();
$messageId = $telegram->Callback_Message()['message_id'];
$firstName = $telegram->FirstName();
$ReplyToMessageID = $telegram->ReplyToMessageID();
$callbackId = $telegram->Callback_ChatID();
$userId = $telegram->UserID();

$durationNum = null;
$stateName = null;
$ageFormated = null;
$currencyName = null;
$timestamp = time('Y-m-d');
$filename = "data/{$userId}_" . gmdate("G-j-n-Y", time()) . ".json";

function saveUserData($chat_id, $key, $value) {
    global $userId;

    $dir = 'data/';
    $files = scandir($dir);

    foreach ($files as $filename) {
        if (strpos($filename, $userId . '_') === 0) {
            $file_path = $dir . $filename;
            $jsonData = file_get_contents($file_path);
            $userData = json_decode($jsonData, true);

            // Обновляем данные пользователя
            if (!isset($userData[$chat_id])) {
                $userData[$chat_id] = [];
            }
            $userData[$chat_id][$key] = $value;

            // Сохраняем обновленные данные обратно в файл
            $jsonData = json_encode($userData);
            file_put_contents($file_path, $jsonData);

            return;
        }
    }

    // Файл не найден, создаем новый файл
    $filename = "data/{$userId}_" . gmdate("G-j-n-Y") . ".json";
    $userData = [$chat_id => [$key => $value]];
    $jsonData = json_encode($userData);
    file_put_contents($filename, $jsonData);
}

if ((!$telegram->messageFromGroup())
    && !is_null($text)
    && !is_null($chat_id)
    && ($text === CALCULATION_PROG . " " . SEP)) {
    $reply = "Не погано)

<b>" .SEP . "</b> це вигідні інвестиції оптимальним шляхом!

Для розрахунку <b>" .SEP . "</b> мені знадобляться деякі дані про Вас.";

    // Create option for the custom keyboard. Array of array string
    $option = [
        [CALCULATION_PROG . " " . SEP],
        [CONSULTATION],
    ];
    // Get the keyboard
    $keyb = $telegram->buildKeyBoard($option, true, true, false);
    $content = [
        'chat_id' => $chat_id,
        'reply_markup' => $keyb,
        'text' => $reply,
        'parse_mode' => "html",
    ];
    $telegram->sendMessage($content);
    $messageId = $telegram->MessageID();

    // Continue Yes.
    $reply = '👇';
    $content = [
        'chat_id' => $chat_id,
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [
                    [
                        'text' => "Продовжимо, " . $firstName . "?",
                        'callback_data' => 'continue_sep',
                    ],
                ],
                [
                    [
                        'text' => "До головного меню",
                        'callback_data' => 'back_menu',
                    ],
                ],
            ],
            'is_persistent' => false,
            'one_time_keyboard' => false,
            'resize_keyboard' => false,
        ]),
        'text' => $reply,
        'parse_mode' => "html",
    ];
    $telegram->sendMessage($content);
    $messageId = $telegram->MessageID();
}

if (!empty($callbackData)) {
//    $chat_id = $telegram->Callback_ChatID(); // Добавьте эту строку

    if ($callbackData === 'continue_sep') {
        $content = [
            'chat_id' => $chat_id,
            'text' => 'Укажiть стать',
            'parse_mode' => "html",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'Жiноча',
                            'callback_data' => 'woman',
                        ],
                        [
                            'text' => 'Чоловiча',
                            'callback_data' => 'man',
                        ],
                    ]
                ],
                'is_persistent' => true,
                'one_time_keyboard' => false,
                'resize_keyboard' => true,
            ]),
        ];
        $telegram->sendMessage($content);
        $messageId = $telegram->MessageID();
    }

    $state = $callbackData;
    if ($state === 'woman') {
        $stateName = 'Жiноча';
    } elseif ($state === 'man') {
        $stateName = 'Чоловiча';
    }

    if ($state === 'woman' || $state === 'man') {
        $content = [
            'chat_id' => $chat_id,
            'text' => 'Ваша стать - <b>' . $stateName . '</b>.',
            'parse_mode' => "html",
            'reply_to_message_id' => $messageId,
        ];
        $telegram->sendMessage($content);
        $messageId = $telegram->MessageID();
        saveUserData($chat_id, 'stateName', $stateName);

        $option = [
            [
                $telegram->buildInlineKeyBoardButton(
                    'Добре, продовжимо?',
                    $url = '',
                    $callback_data = 'age'
                ),
            ],
        ];

        $keyb = $telegram->buildInlineKeyBoard($option);

        $content = [
            'chat_id' => $chat_id,
            'text' => '👇',
            'parse_mode' => "html",
            'reply_markup' => $keyb,
        ];
        $telegram->sendMessage($content);
        $messageId = $telegram->MessageID();
    }

    if ($callbackData === 'age') {
        $content = [
            'chat_id' => $chat_id,
            'text' => '👇 Далi введіть вік на початку страхування. 👇
<i>* Мінімальний вік для страхування – 15 років.
* Максiмальний вік для страхування – 60 років.</i>',
            'parse_mode' => "html",
            'reply_markup' => $telegram->buildForceReply(),
        ];

        $telegram->sendMessage($content);
        $messageId = $telegram->MessageID();
    }
}

/* ================================================== */

if (!$telegram->messageFromGroup()) {
    $age = $text;
    if ($ReplyToMessageID) {
        // Response to age request.


        $ageFormated =& $age;
        unset($age);
        $ageFormated = number_format($ageFormated);
        if ($ageFormated >= 15 && $ageFormated <= 60) {
            $content = [
                'chat_id' => $chat_id,
                'text' => 'Ваш вiк, повних рокiв - <b>' . $ageFormated . '</b>.',
                'parse_mode' => "html",
                'reply_to_message_id' => $messageId,
            ];
            $telegram->sendMessage($content);
            $messageId = $telegram->MessageID();
            saveUserData($chat_id, 'ageFormated', $ageFormated);

            $content = [
                'chat_id' => $chat_id,
                'text' => '👇',
                'parse_mode' => "html",
//                'reply_to_message_id' => $messageId,
            ];
            $telegram->sendMessage($content);
            $messageId = $telegram->MessageID();

            $content = [
                'chat_id' => $chat_id,
                'text' => '👇 Будь ласка, оберіть термін дії страховки 👇',
                'parse_mode' => "html",
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => '5', 'callback_data' => 'duration_5'],
                            ['text' => '10', 'callback_data' => 'duration_10'],
                            ['text' => '15', 'callback_data' => 'duration_15'],
                            ['text' => '20', 'callback_data' => 'duration_20'],
                            ['text' => '25', 'callback_data' => 'duration_25'],
                            ['text' => '30', 'callback_data' => 'duration_30'],
                        ],
                    ],
                    'is_persistent' => true,
                    'one_time_keyboard' => false,
                    'resize_keyboard' => true,
                ]),
            ];
            $telegram->sendMessage($content);
            $messageId = $telegram->MessageID();

        } else {
            $content = [
                'chat_id' => $chat_id,
                'text' => 'Необхідно ввести повне число від 15 до 60',
                'parse_mode' => "html",
                'reply_to_message_id' => $messageId,
                'reply_markup' => $telegram->buildForceReply(),
            ];
            $telegram->sendMessage($content);
            $messageId = $telegram->MessageID();
        }

    }
}

if (!empty($callbackData)) {
    $duration = $callbackData;

    $chat_id = $telegram->Callback_ChatID();

    if (!empty($callbackData)) {
        $chat_id = $telegram->Callback_ChatID();

        if (strpos($callbackData, 'duration_') === 0) {
            $durationNum = substr($callbackData, 9);

            $content = [
                'chat_id' => $chat_id,
                'text' => 'Ваш термін страхування <b>' . $durationNum . '</b> рокiв.',
                'parse_mode' => "html",
                'reply_to_message_id' => $messageId,
            ];
            $telegram->sendMessage($content);
            $messageId = $telegram->MessageID();
            saveUserData($chat_id, 'durationNum', $durationNum);
        }
    }

    if (substr($duration, 0, 8) === 'duration') {
        $content = [
            'chat_id' => $chat_id,
            'text' => '👇',
            'parse_mode' => "html",
        ];
        $telegram->sendMessage($content);
        $messageId = $telegram->MessageID();

        $content = [
            'chat_id' => $chat_id,
            'text' => '👇 Оберiть валюту страховки 👇',
            'parse_mode' => "html",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'Гривня', 'callback_data' => 'cur_hrivnya'],
                        ['text' => 'Доллар США', 'callback_data' => 'cur_dollar'],
                    ],
                ],
                'is_persistent' => true,
                'one_time_keyboard' => false,
                'resize_keyboard' => true,
            ]),
        ];
        $telegram->sendMessage($content);
        $messageId = $telegram->MessageID();
    }

    if (!empty($callbackData)) {
        $chat_id = $telegram->Callback_ChatID();

        if (strpos($callbackData, 'cur_') === 0) {
            if ($callbackData === 'cur_hrivnya') {
                $currencyName = 'Гривня';
            } else {
                $currencyName = 'Доллар США';
            }

            $content = [
                'chat_id' => $chat_id,
                'text' => 'Валюта страховки <b>' . $currencyName . '</b>',
                'parse_mode' => "html",
                'reply_to_message_id' => $messageId,
            ];
            $telegram->sendMessage($content);
            $messageId = $telegram->MessageID();
            saveUserData($chat_id, 'currencyName', $currencyName);

            $content = [
                'chat_id' => $chat_id,
                'text' => '👇',
                'parse_mode' => "html",
            ];
            $telegram->sendMessage($content);
            $messageId = $telegram->MessageID();

        }
    }
}

/* ================================================== */

$userId = $telegram->UserID();
$jsonData = file_get_contents($filename);

$dataArray = json_decode($jsonData, true);

$stateNameJson = $dataArray[$userId]['stateName'];
$ageFormatedJson = $dataArray[$userId]['ageFormated'];
$durationNumJson = $dataArray[$userId]['durationNum'];
$currencyNameJson = $dataArray[$userId]['currencyName'];


//file_put_contents(
//    'log.txt',
//    'Термiн: ' . $dataArray . PHP_EOL,
//    FILE_APPEND
//);
$fromCallbackQuery = false;

if (
    isset($stateNameJson) &&
    isset($ageFormatedJson) &&
    isset($durationNumJson) &&
    isset($currencyNameJson) &&
    ($callbackData === 'cur_dollar' || $callbackData === 'cur_hrivnya')
) {
    $content = [
        'chat_id' => $chat_id,
        'text' => 'Ви ввели даннi:
Ваша стать - <b>' . $stateNameJson . '</b>.
Ваш вiк, повних рокiв - <b>' . $ageFormatedJson . '</b>.
Ваш термін страхування, рокiв <b>' . $durationNumJson . '</b>.
Валюта страховки <b>' . $currencyNameJson . '</b>.',
        'parse_mode' => "html",
    ];
    $telegram->sendMessage($content);
    $messageId = $telegram->MessageID();

    $content = [
        'chat_id' => $chat_id,
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [
                    [
                        'text' => 'Розрахувати',
                        'callback_data' => 'calculation'
                    ],
                ],
                [
                    [
                        'text' => 'Повернутись на початок розрахунку',
                        'callback_data' => 'back_calculation'
                    ],
                ],
                [
                    [
                        'text' => 'Повернутись на головне меню',
                        'callback_data' => 'back_menu'
                    ],
                ],
            ],
            'is_persistent' => false,
            'one_time_keyboard' => false,
            'resize_keyboard' => false,
        ]),
        'text' => '👇 Все вiрно, ' . $firstName . ', розрахувати? 👇',
        'parse_mode' => "html",
    ];
    $telegram->sendMessage($content);
    $messageId = $telegram->MessageID();
}

// Обработка CallbackQuery
$update = json_decode(file_get_contents("php://input"), true);
if (isset($update["callback_query"])) {
    $fromCallbackQuery = true;
    $callbackQuery = $update["callback_query"];
    $callbackData = $callbackQuery["data"];
    $callbackChatId = $callbackQuery["message"]["chat"]["id"];

    if ($callbackData === 'calculation') {
        // Расчет страховки
        $content = [
            'chat_id' => $callbackChatId,
            'text' => 'Делаю расчет',
        ];
        $telegram->sendMessage($content);

    } elseif (($callbackData === 'back_calculation') || ($callbackData === 'back_menu')) {

        // Delete JSON file.
        $files = glob('data/' . $userId . '*.json'); // Убедитесь, что путь к папке указан верно
        foreach ($files as $file) {
            unlink($file);
        }
        // В зависимости от $callbackData перенаправьте пользователя на соответствующий шаг
    }

    // CallbackQuery response.
    $telegram->answerCallbackQuery([
        'callback_query_id' => $callbackQuery['id'],
        'text' => '',
        'show_alert' => false,
    ]);
}
