<?php

include_once 'settings.php';

// Instances the class.
$telegram = new Telegram(TG_TOKEN);

// Take text and chat_id from the message.
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
$summName = null;
$timestamp = time('Y-m-d');
$filename = "data/{$userId}_" . gmdate("G-j-n-Y", time()) . ".json";

$userId = $telegram->UserID();
$jsonData = file_get_contents($filename);

$dataArray = json_decode($jsonData, true);

$stateNameJson = $dataArray[$userId]['stateName'];
$ageFormatedJson = $dataArray[$userId]['ageFormated'];
$durationNumJson = $dataArray[$userId]['durationNum'];
$currencyNameJson = $dataArray[$userId]['currencyName'];
$summNameJson = $dataArray[$userId]['summName'];
$summInshNameJson = $dataArray[$userId]['summInshName'];

function saveUserData($chat_id, $key, $value) {
    global $userId;

    $dir = 'data/';
    $files = scandir($dir);

    foreach ($files as $filename) {
        if (strpos($filename, $userId . '_') === 0) {
            $file_path = $dir . $filename;
            $jsonData = file_get_contents($file_path);
            $userData = json_decode($jsonData, true);

            // User data.
            if (!isset($userData[$chat_id])) {
                $userData[$chat_id] = [];
            }
            $userData[$chat_id][$key] = $value;

            // Save data back to file.
            $jsonData = json_encode($userData);
            file_put_contents($file_path, $jsonData);

            return;
        }
    }

    // File wasn`t find, new file created.
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
    if ($callbackData === 'continue_sep') {
        $content = [
            'chat_id' => $chat_id,
            'text' => 'Вкажiть стать',
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
            'text' => '👇 Далi оберiть вік на початку страхування. 👇
<i>* Мінімальний вік для страхування – 15 років.
* Максiмальний вік для страхування – 60 років.</i>',
            'parse_mode' => "html",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => '15', 'callback_data' => 'age_15'],
                        ['text' => '20', 'callback_data' => 'age_20'],
                        ['text' => '25', 'callback_data' => 'age_25'],
                        ['text' => '30', 'callback_data' => 'age_30'],
                        ['text' => '35', 'callback_data' => 'age_35'],
                    ],
                    [
                        ['text' => '40', 'callback_data' => 'age_40'],
                        ['text' => '45', 'callback_data' => 'age_45'],
                        ['text' => '50', 'callback_data' => 'age_50'],
                        ['text' => '55', 'callback_data' => 'age_55'],
                        ['text' => '60', 'callback_data' => 'age_60'],
                    ],
                ],
                'is_persistent' => true,
                'one_time_keyboard' => false,
                'resize_keyboard' => false,
            ]),
        ];

        $telegram->sendMessage($content);
        $messageId = $telegram->MessageID();
    }
}

/* ================================================== */

    if ($telegram->Callback_Query()) {
        $callbackData = $telegram->Callback_Data();

        if (strpos($callbackData, 'age_') === 0) {

            $age = $callbackData;
            $ageFormated = substr($age, 4);
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
            ];
            $telegram->sendMessage($content);
            $messageId = $telegram->MessageID();

            $ageDiff = 70 - $ageFormated;

            switch ($ageFormated) {
                case '15':
                    $buttons = [
                        [
                            ['text' => '10', 'callback_data' => 'duration_10'],
                            ['text' => '15', 'callback_data' => 'duration_15'],
                            ['text' => '20', 'callback_data' => 'duration_20'],
                            ['text' => '25', 'callback_data' => 'duration_25'],
                            ['text' => '30', 'callback_data' => 'duration_30'],
                        ],
                        [
                            ['text' => '35', 'callback_data' => 'duration_35'],
                            ['text' => '40', 'callback_data' => 'duration_40'],
                            ['text' => '45', 'callback_data' => 'duration_45'],
                            ['text' => '50', 'callback_data' => 'duration_50'],
                            ['text' => '55', 'callback_data' => 'duration_55'],
                        ]
                    ];
                    break;

                case '20':
                    $buttons = [
                        [
                            ['text' => '10', 'callback_data' => 'duration_10'],
                            ['text' => '15', 'callback_data' => 'duration_15'],
                            ['text' => '20', 'callback_data' => 'duration_20'],
                            ['text' => '25', 'callback_data' => 'duration_25'],
                            ['text' => '30', 'callback_data' => 'duration_30'],
                        ],
                        [
                            ['text' => '35', 'callback_data' => 'duration_35'],
                            ['text' => '40', 'callback_data' => 'duration_40'],
                            ['text' => '45', 'callback_data' => 'duration_45'],
                            ['text' => '50', 'callback_data' => 'duration_50'],
                        ]
                    ];
                    break;

                case '25':
                    $buttons = [
                        [
                            ['text' => '10', 'callback_data' => 'duration_10'],
                            ['text' => '15', 'callback_data' => 'duration_15'],
                            ['text' => '20', 'callback_data' => 'duration_20'],
                            ['text' => '25', 'callback_data' => 'duration_25'],
                        ],
                        [
                            ['text' => '30', 'callback_data' => 'duration_30'],
                            ['text' => '35', 'callback_data' => 'duration_35'],
                            ['text' => '40', 'callback_data' => 'duration_40'],
                            ['text' => '45', 'callback_data' => 'duration_45'],
                        ]
                    ];
                    break;

                case '30':
                    $buttons = [
                        [
                            ['text' => '10', 'callback_data' => 'duration_10'],
                            ['text' => '15', 'callback_data' => 'duration_15'],
                            ['text' => '20', 'callback_data' => 'duration_20'],
                            ['text' => '25', 'callback_data' => 'duration_25'],
                        ],
                        [
                            ['text' => '30', 'callback_data' => 'duration_30'],
                            ['text' => '35', 'callback_data' => 'duration_35'],
                            ['text' => '40', 'callback_data' => 'duration_40'],
                        ]
                    ];
                    break;

                case '35':
                    $buttons = [
                        [
                            ['text' => '10', 'callback_data' => 'duration_10'],
                            ['text' => '15', 'callback_data' => 'duration_15'],
                            ['text' => '20', 'callback_data' => 'duration_20'],

                        ],
                        [
                            ['text' => '25', 'callback_data' => 'duration_25'],
                            ['text' => '30', 'callback_data' => 'duration_30'],
                            ['text' => '35', 'callback_data' => 'duration_35'],
                        ]
                    ];
                    break;

                case '40':
                    $buttons = [
                        [
                            ['text' => '10', 'callback_data' => 'duration_10'],
                            ['text' => '15', 'callback_data' => 'duration_15'],
                        ],
                        [
                            ['text' => '20', 'callback_data' => 'duration_20'],
                            ['text' => '25', 'callback_data' => 'duration_25'],
                            ['text' => '30', 'callback_data' => 'duration_30'],
                        ]
                    ];
                    break;

                case '45':
                    $buttons = [
                        [
                            ['text' => '10', 'callback_data' => 'duration_10'],
                            ['text' => '15', 'callback_data' => 'duration_15'],
                            ['text' => '20', 'callback_data' => 'duration_20'],
                            ['text' => '25', 'callback_data' => 'duration_25'],
                        ],
                    ];
                    break;

                case '50':
                    $buttons = [
                        [
                            ['text' => '10', 'callback_data' => 'duration_10'],
                            ['text' => '15', 'callback_data' => 'duration_15'],
                            ['text' => '20', 'callback_data' => 'duration_20'],
                        ],
                    ];
                    break;

                case '55':
                    $buttons = [
                        [
                            ['text' => '10', 'callback_data' => 'duration_10'],
                            ['text' => '15', 'callback_data' => 'duration_15'],
                        ],
                    ];
                    break;

                case '60':
                    $buttons = [
                        [
                            ['text' => '10', 'callback_data' => 'duration_10'],
                        ],
                    ];
                    break;
            }

            $content = [
                'chat_id' => $chat_id,
                'text' => '👇 Будь ласка, оберіть термін дії страховки 👇',
                'parse_mode' => "html",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $buttons,
                    'is_persistent' => true,
                    'one_time_keyboard' => false,
                    'resize_keyboard' => true,
                ]),
            ];
            $telegram->sendMessage($content);
            $messageId = $telegram->MessageID();
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

        $buttons = [];
        if ($currencyName === 'Доллар США') {
            switch ($durationNumJson) {
                case 10:
                    $buttons = [
                        [
                            ['text' => '300', 'callback_data' => 'sum_300'],
                            ['text' => '1000', 'callback_data' => 'sum_1000'],
                            ['text' => '3000', 'callback_data' => 'sum_3000'],
                        ],
                        [
                            ['text' => '5000', 'callback_data' => 'sum_5000'],
                            ['text' => '7000', 'callback_data' => 'sum_7000'],
                            ['text' => '8000', 'callback_data' => 'sum_8000'],
                        ],
                    ];
                    break;
                case 15:
                    $buttons = [
                        [
                            ['text' => '300', 'callback_data' => 'sum_300'],
                            ['text' => '1000', 'callback_data' => 'sum_1000'],
                            ['text' => '3000', 'callback_data' => 'sum_3000'],
                        ],
                        [
                            ['text' => '4000', 'callback_data' => 'sum_4000'],
                            ['text' => '5000', 'callback_data' => 'sum_5000'],
                            ['text' => '5500', 'callback_data' => 'sum_5500'],
                        ],
                    ];
                    break;
                case 20:
                    $buttons = [
                        [
                            ['text' => '300', 'callback_data' => 'sum_300'],
                            ['text' => '800', 'callback_data' => 'sum_800'],
                            ['text' => '1000', 'callback_data' => 'sum_1000'],
                        ],
                        [
                            ['text' => '2000', 'callback_data' => 'sum_2000'],
                            ['text' => '3000', 'callback_data' => 'sum_3000'],
                            ['text' => '4000', 'callback_data' => 'sum_4000'],
                        ],
                    ];
                    break;
                case 25:
                    $buttons = [
                        [
                            ['text' => '300', 'callback_data' => 'sum_300'],
                            ['text' => '1000', 'callback_data' => 'sum_1000'],
                            ['text' => '2000', 'callback_data' => 'sum_2000'],
                            ['text' => '3000', 'callback_data' => 'sum_3000'],
                        ],
                    ];
                    break;
                case 30:
                    $buttons = [
                        [
                            ['text' => '300', 'callback_data' => 'sum_300'],
                            ['text' => '1000', 'callback_data' => 'sum_1000'],
                            ['text' => '2000', 'callback_data' => 'sum_2000'],
                            ['text' => '2600', 'callback_data' => 'sum_2600'],
                        ],
                    ];
                    break;

                case 35:
                    $buttons = [
                        [
                            ['text' => '300', 'callback_data' => 'sum_300'],
                            ['text' => '1000', 'callback_data' => 'sum_1000'],
                            ['text' => '1600', 'callback_data' => 'sum_1600'],
                            ['text' => '2200', 'callback_data' => 'sum_2200'],
                        ],
                    ];
                    break;

                case 40:
                    $buttons = [
                        [
                            ['text' => '300', 'callback_data' => 'sum_300'],
                            ['text' => '1000', 'callback_data' => 'sum_1000'],
                            ['text' => '1400', 'callback_data' => 'sum_1400'],
                            ['text' => '2000', 'callback_data' => 'sum_2000'],
                        ],
                    ];
                    break;

                case 45:
                    $buttons = [
                        [
                            ['text' => '300', 'callback_data' => 'sum_300'],
                            ['text' => '1000', 'callback_data' => 'sum_1000'],
                            ['text' => '1400', 'callback_data' => 'sum_1400'],
                            ['text' => '1800', 'callback_data' => 'sum_1800'],
                        ],
                    ];
                    break;

                case 50:
                    $buttons = [
                        [
                            ['text' => '300', 'callback_data' => 'sum_300'],
                            ['text' => '1000', 'callback_data' => 'sum_1000'],
                            ['text' => '1200', 'callback_data' => 'sum_1200'],
                            ['text' => '1600', 'callback_data' => 'sum_1600'],
                        ],
                    ];
                    break;

                case 55:
                    $buttons = [
                        [
                            ['text' => '300', 'callback_data' => 'sum_300'],
                            ['text' => '1000', 'callback_data' => 'sum_1000'],
                            ['text' => '1200', 'callback_data' => 'sum_1200'],
                            ['text' => '1500', 'callback_data' => 'sum_1500'],
                        ],
                    ];
                    break;

            }
        } elseif ($currencyName === 'Гривня') {
            switch ($durationNumJson) {
                case 5:
                    // Кнопки для $durationNum = 10
                    $buttons = [
                        [
                            ['text' => '100', 'callback_data' => 'sum_100'],
                        ],
                    ];
                    break;
                case 10:
                    $buttons = [
                        [
                            ['text' => '200', 'callback_data' => 'sum_200'],
                        ],
                    ];
                    break;
                case 15:
                    $buttons = [
                        [
                            ['text' => '300', 'callback_data' => 'sum_300'],
                        ],
                    ];
                    break;
                case 20:
                    // Кнопки для $durationNum = 10
                    $buttons = [
                        [
                            ['text' => '400', 'callback_data' => 'sum_400'],
                        ],
                    ];
                    break;
                case 25:
                    // Кнопки для $durationNum = 20
                    $buttons = [
                        [
                            ['text' => '300', 'callback_data' => 'sum_300'],
                            ['text' => '600', 'callback_data' => 'sum_600'],
                            ['text' => '800', 'callback_data' => 'sum_800'],
                            ['text' => '1000', 'callback_data' => 'sum_1000'],
                            ['text' => '1200', 'callback_data' => 'sum_1200'],
                            ['text' => '1400', 'callback_data' => 'sum_1400'],
                        ],
                        [
                            ['text' => '1600', 'callback_data' => 'sum_1600'],
                            ['text' => '1800', 'callback_data' => 'sum_1800'],
                            ['text' => '2000', 'callback_data' => 'sum_2000'],
                            ['text' => '2200', 'callback_data' => 'sum_2200'],
                            ['text' => '2400', 'callback_data' => 'sum_2400'],

                        ],
                        [
                            ['text' => '2600', 'callback_data' => 'sum_2600'],
                            ['text' => '2800', 'callback_data' => 'sum_2800'],
                            ['text' => '3000', 'callback_data' => 'sum_3000'],
                            ['text' => '3200', 'callback_data' => 'sum_3200'],
                        ],
                    ];
                    break;
                case 30:
                    $buttons = [
                        [
                            ['text' => '300', 'callback_data' => 'sum_300'],
                            ['text' => '600', 'callback_data' => 'sum_600'],
                            ['text' => '800', 'callback_data' => 'sum_800'],
                            ['text' => '1000', 'callback_data' => 'sum_1000'],
                            ['text' => '1200', 'callback_data' => 'sum_1200'],
                            ['text' => '1400', 'callback_data' => 'sum_1400'],
                        ],
                        [
                            ['text' => '1600', 'callback_data' => 'sum_1600'],
                            ['text' => '1800', 'callback_data' => 'sum_1800'],
                            ['text' => '2000', 'callback_data' => 'sum_2000'],
                            ['text' => '2200', 'callback_data' => 'sum_2200'],
                            ['text' => '2400', 'callback_data' => 'sum_2400'],
                            ['text' => '2600', 'callback_data' => 'sum_2600'],
                        ],
                    ];
                    break;
            }
        }

        $content = [
            'chat_id' => $chat_id,
            'text' => '👇 Оберiть суму щорічного внеску. 👇
            Мінімальний щорічний внесок: 3000 грн, або $300',
            'parse_mode' => "html",
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons,
                'is_persistent' => true,
                'one_time_keyboard' => false,
                'resize_keyboard' => true,
            ]),
        ];

        $telegram->sendMessage($content);

        /*---  Debuging ---*/
        function saveDebugInfo($debugLogFile, $data) {
            $formattedData = print_r($data, true);
            $timestamp = date('Y-m-d H:i:s');
            file_put_contents($debugLogFile, $timestamp . ' - ' . $formattedData . PHP_EOL, FILE_APPEND);
        }
    }
}

if (strpos($callbackData, 'sum_') === 0) {
    $summ = $callbackData;
    $chat_id = $telegram->Callback_ChatID();
    $summName = substr($callbackData, 4);

    $content = [
        'chat_id' => $chat_id,
        'text' => 'Сума щорічного внеску<b> ' . $summName . '</b>',
        'parse_mode' => "html",
        'reply_to_message_id' => $messageId,
    ];
    saveUserData($chat_id, 'summName', $summName);
    $telegram->sendMessage($content);
    $messageId = $telegram->MessageID();

$fromCallbackQuery = false;

    $content = [
        'chat_id' => $chat_id,
        'text' => 'Ви ввели даннi:
Ваша стать - <b>' . $stateNameJson . '</b>.
Ваш вiк, повних рокiв - <b>' . $ageFormatedJson . '</b>.
Ваш термін страхування, рокiв<b> ' . $durationNumJson . '</b>.
Валюта страховки <b> ' . $currencyNameJson . '</b>.
Сума щорічного внеску<b> ' . $summNameJson . '</b>.',
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
                        'callback_data' => 'continue_sep'
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
/* ================================================== */

// CallbackQuery.
$update = json_decode(file_get_contents("php://input"), true);
if (isset($update["callback_query"])) {
    $fromCallbackQuery = true;
    $callbackQuery = $update["callback_query"];
    $callbackData = $callbackQuery["data"];
    $callbackChatId = $callbackQuery["message"]["chat"]["id"];

    if ($callbackData === 'calculation') {
        // Insurance calc.

        // Load a local file to upload. If is already on Telegram's Servers just pass the resource id
        $gif = curl_file_create('images/waiting-1-min.gif', 'image/gif');
        $content = array('chat_id' => $callbackChatId, 'animation' => $gif);

        $response = $telegram->sendAnimation($content);
        $messageIdToDelete = $response['result']['message_id'];

        // delay callback.
        usleep(6000000);

        $telegram->deleteMessage([
            'chat_id' => $chat_id,
            'message_id' => $messageIdToDelete
        ]);

        $content = [
            'chat_id' => $chat_id,
            'text' => '👇',
            'parse_mode' => "html",
        ];
        $telegram->sendMessage($content);
        $messageId = $telegram->MessageID();

        /* -- Insert calculation -- */
        $result = ($durationNumJson * $summNameJson);

        // Add condition for $result.
        if ($stateNameJson === 'Жiноча') {
            $result *= 1.0;
        } elseif ($stateNameJson === 'Чоловiча') {
            $result *= 0.88044;
        }

        if ($currencyNameJson === 'Доллар США') {
            switch ($ageFormatedJson) {
                case 15:
                    $coef = 1;
                    break;
                case 20:
                    $coef = 1;
                    break;
                case 25:
                    $coef = 1;
                    break;
                case 30:
                    $coef = 0.9874;
                    break;
                case 35:
                    $coef = 0.9740;
                    break;
                case 40:
                    $coef = 0.9523;
                    break;
                case 45:
                    $coef = 0.9189;
                    break;
                case 50:
                    $coef = 0.8733;
                    break;
                case 55:
                    $coef = 0.8182;
                    break;
                case 60:
                    $coef = 0.7582;
                    break;
                default:
                    $coef = 1;
                    break;
            }

            $result *= $coef;
            $resultInshSumm = $result;

            switch ($durationNumJson) {
                case 10:
                    switch ($summNameJson) {
                        case 300:
                            $result /= 1.0530;
                            break;
                        case 1000:
                            $result /= 1.0080;
                            break;
                        case 1500:
                            $result /= 1.0013;
                            break;
                        case 5000:
                            $result /= 0.9827;
                            break;
                        case 7000:
                            $result /= 0.9723;
                            break;
                        case 8000:
                            $result /= 0.9720;
                            break;
                        default:
                            break;
                    }
                    $resultInshSumm = $result * 0.9235;
                    break;

                case 15:
                    switch ($summNameJson) {
                        case 300:
                            $result /= 1.0160;
                            break;
                        case 1000:
                            $result /= 0.9610;
                            break;
                        case 3000:
                            $result /= 0.9335;
                            break;
                        case 4000:
                            $result /= 0.9184;
                            break;
                        case 5000:
                            $result /= 0.9170;
                            break;
                        case 5500:
                            $result /= 0.9174;
                            break;
                        default:
                            break;
                    }
                    $resultInshSumm = $result * 0.8889;
                    break;

                case 20:
                    switch ($summNameJson) {
                        case 300:
                            $result /= 0.9483;
                            break;
                        case 800:
                            $result /= 0.9252;
                            break;
                        case 1000:
                            $result /= 0.9194;
                            break;
                        case 2000:
                            $result /= 0.8908;
                            break;
                        case 3000:
                            $result /= 0.8699;
                            break;
                        case 4000:
                            $result /= 0.8682;
                            break;
                        default:
                            break;
                    }
                    $resultInshSumm = $result * 0.8544;
                    break;

                case 25:
                    switch ($summNameJson) {
                        case 300:
                            $result /= 0.9129;
                            break;
                        case 1000:
                            $result /= 0.8601;
                            break;
                        case 2000:
                            $result /= 0.8481;
                            break;
                        case 3000:
                            $result /= 0.8235;
                            break;
                        default:
                            break;
                    }
                    $resultInshSumm = $result * 0.8200;
                    break;

                case 30:
                    switch ($summNameJson) {
                        case 300:
                            $result /= 0.8850;
                            break;
                        case 1000:
                            $result /= 0.8254;
                            break;
                        case 2000:
                            $result /= 0.7883;
                            break;
                        case 2600:
                            $result /= 0.7854;
                            break;
                        default:
                            break;
                    }
                    $resultInshSumm = $result * 0.7860;
                    break;

                case 35:
                    switch ($summNameJson) {
                        case 300:
                            $result /= 0.8578;
                            break;
                        case 1000:
                            $result /= 0.7917;
                            break;
                        case 1600:
                            $result /= 0.7539;
                            break;
                        case 2200:
                            $result /= 0.7492;
                            break;
                        default:
                            break;
                    }
                    $resultInshSumm = $result * 0.7521;
                    break;

                case 40:
                    switch ($summNameJson) {
                        case 300:
                            $result /= 0.8392;
                            break;
                        case 1000:
                            $result /= 0.7663;
                            break;
                        case 1400:
                            $result /= 0.7277;
                            break;
                        case 2000:
                            $result /= 0.7213;
                            break;
                        default:
                            break;
                    }
                    $resultInshSumm = $result * 0.7183;
                    break;

                case 45:
                    switch ($summNameJson) {
                        case 300:
                            $result /= 0.8202;
                            break;
                        case 1000:
                            $result /= 0.7411;
                            break;
                        case 1400:
                            $result /= 0.6995;
                            break;
                        case 1800:
                            $result /= 0.6944;
                            break;
                        default:
                            break;
                    }
                    $resultInshSumm = $result * 0.6840;
                    break;

                case 50:
                    switch ($summNameJson) {
                        case 300:
                            $result /= 0.8013;
                            break;
                        case 1000:
                            $result /= 0.7168;
                            break;
                        case 1200:
                            $result /= 0.6769;
                            break;
                        case 1600:
                            $result /= 0.6698;
                            break;
                        default:
                            break;
                    }
                    $resultInshSumm = $result * 0.6493;
                    break;

                case 55:
                    switch ($summNameJson) {
                        case 300:
                            $result /= 0.7886;
                            break;
                        case 1000:
                            $result /= 0.6629;
                            break;
                        case 1200:
                            $result /= 0.6568;
                            break;
                        case 1500:
                            $result /= 0.6507;
                            break;
                        default:
                            break;
                    }
                    $resultInshSumm = $result * 0.6134;
                    break;

                default:
                    break;
            }
        }

                $content = [
            'chat_id' => $chat_id,
            'text' => '<b>Програма СЕП пропонує Вам широкий страховий захист і вигідні інвестиції оптимальним шляхом!</b>

<i>Страхова економічна програма (СЕП) складається з 5 тарифів:</i>
✅ GX5S – Страхування на випадок смерті та дожиття із виплатою диференційованої страхової суми на момент смерті та участю у додатковому прибутку.

✅ UTZ – Додаткове страхування на випадок смерті в результаті нещасного випадку. Страхова сума виплачується в разі, якщо смерть застрахованої особи настала в результаті нещасного випадку протягом узгодженого терміну дії договору.

✅ UI50P – Додаткове страхування на випадок тривалої інвалідності в результаті нещасного випадку у розмірі від 50%. В разі, якщо ступінь інвалідності становить 50% і більше, то страховик бере на себе обов’язок сплачувати страхові премії за страхувальника і обумовлена страхова виплата разом із додатковим прибутком здійснюється по закінченню терміну дії договору.

✅ UI100 – Додаткове страхування на випадок повної тривалої інвалідності в результаті нещасного випадку. Страхова сума виплачується у повному обсязі, якщо ступінь тривалої інвалідності складає 100%.

✅ RXZ – Додаткове страхування на випадок смерті за будь-якої причини. Страхова сума виплачується у разі смерті застрахованої особи протягом узгодженого терміну дії договору. Страхова сума за тарифом складає 2000 USD або 3000 USD (10000 UAH або 15000 UAH) в залежності від розміру страхової премії – більше, або менше 500 USD (2500 UAH).

Ваша стать - <b>' . $stateNameJson . '</b>.
Ваш вiк, повних рокiв - <b>' . $ageFormatedJson . '</b>.
Ваш термін страхування, рокiв <b>' . $durationNumJson . '</b>.
Валюта страховки <b>' . $currencyNameJson . '</b>.
Сума щорічного внеску <b>' . $summNameJson . '</b>.

✔️-------------------✔️
<i>Результати розрахунків є приблизними.
Для отримання більш точних результатів Ви можете скористатись калькулятором на сайті Граве https://online.grawe.ua/SEP</i>
✔️-------------------✔️

<b>💷 Капітал* становить:
 ' . $currencyNameJson . ' <u>' .  number_format($result, 2, '.', '') . '</u></b>
*Капітал - сума, яку Ви отримаєте по закiнченню терміна страхування.

<b>💰 Страхова сума* становить:
 ' . $currencyNameJson . ' <u>' .  number_format($resultInshSumm, 2, '.', '') . '</u></b>
*Страхова сума - сума, яку отримає Вигодонабувач на разi страхового випадку.',
            'parse_mode' => "html",
        ];
        $calculationResult = $content['text'];
        $telegram->sendMessage($content);
        $messageId = $telegram->MessageID();
        saveUserData($chat_id, 'result', $result);

        $content = [
            'chat_id' => $chat_id,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => CALL_LORA,
                            'url' => 'https://t.me/larisa_lonchar',
                        ],
                    ],
                    [
                        [
                            'text' => '⬆️ Повернутись на початок розрахунку',
                            'callback_data' => 'continue_sep'
                        ],
                    ],
                    [
                        [
                            'text' => '⬅️ Повернутись на головне меню',
                            'callback_data' => 'back_menu'
                        ],
                    ],
                ],
                'is_persistent' => false,
                'one_time_keyboard' => false,
                'resize_keyboard' => false,
            ]),
            'text' => 'Виберіть наступнi дії',
            'parse_mode' => "html",
        ];
        $telegram->sendMessage($content);
        $messageId = $telegram->MessageID();

    } elseif (($callbackData === 'continue_sep') || ($callbackData === 'back_menu')) {

        // Delete JSON file.
        $files = glob('data/' . $userId . '*.json');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    // CallbackQuery response.
    $telegram->answerCallbackQuery([
        'callback_query_id' => $callbackQuery['id'],
        'text' => '',
        'show_alert' => false,
    ]);
}
