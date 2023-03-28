<?php

include_once 'settings.php';

// Instances the class
$telegram = new Telegram(TG_TOKEN);

// Take text and chat_id from the message
$text = $telegram->Text();
$chat_id = $telegram->ChatID();
$result = $telegram->getData();
$callback_query = $telegram->Callback_Query();

if (!empty($callback_query['data'])) {
    if ($callback_query['data'] === 'continue_sep') {
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
    }

    $messageId = $callback_query['message']['message_id'];
    $state = $callback_query['data'];
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
//            'reply_markup' => $telegram->buildForceReply(),
        ];
        $telegram->sendMessage($content);

        $option = [
            [
                $telegram->buildInlineKeyBoardButton(
                    '👇 Далi введіть вік на початку страхування. 👇',
                    $url = '',
                    $callback_data = 'age'
                ),
//                $telegram->buildInlineKeyBoardButton('Доллар США', $url = '', $callback_data = 'dollar'),
            ],
        ];

        $keyb = $telegram->buildInlineKeyBoard($option);
        $content = [
            'chat_id' => $chat_id,
            'text' => '👇',
            'parse_mode' => "html",
//            'reply_to_message_id' => $messageId,
            'reply_markup' => $keyb,
        ];
        $telegram->sendMessage($content);
    }

    if ($callback_query['data'] === 'age') {
        $content = [
            'chat_id' => $chat_id,
            'text' => '<i>* Мінімальний вік для страхування – 15 років.
* Максiмальний вік для страхування – 60 років.</i>',
            'parse_mode' => "html",
//            'reply_to_message_id' => $messageId,
            'reply_markup' => $telegram->buildForceReply(),
        ];

        $telegram->sendMessage($content);
    }

}

/* ================================================== */

$ReplyToMessageID = $result['message']['reply_to_message']['message_id'];
$messageId = $callback_query['message']['message_id'];

$age = $text;
if ($ReplyToMessageID) {
    // Response to age request.
    $content = [
        'chat_id' => $chat_id,
        'text' => '👇',
        'parse_mode' => "html",
        'reply_to_message_id' => $messageId,
        'reply_markup' => $telegram->buildForceReply(),
    ];
    $telegram->sendMessage($content);

    $ageFormated = number_format($age);
    if ($ageFormated >= 15 && $ageFormated <= 60) {
        $content = [
            'chat_id' => $chat_id,
            'text' => 'Ваш вiк, повних рокiв - <b>' . $ageFormated . '</b>.',
            'parse_mode' => "html",
            'reply_to_message_id' => $messageId,
            'reply_markup' => $telegram->buildForceReply(),
        ];
        $telegram->sendMessage($content);

        $content = [
            'chat_id' => $chat_id,
            'text' => '👇 Далi введіть термiн дії страховки 👇

<i>* Мінімальний термін страхування 10 років
* Максiмальний термін страхування 30 років</i>',
            'parse_mode' => "html",
            'reply_to_message_id' => $messageId,
            'reply_markup' => $telegram->buildForceReply(),
        ];
        $telegram->sendMessage($content);

    } else {
        $content = [
            'chat_id' => $chat_id,
            'text' => 'Необхідно ввести повне число від 15 до 60',
            'parse_mode' => "html",
            'reply_to_message_id' => $messageId,
            'reply_markup' => $telegram->buildForceReply(),
        ];
        $telegram->sendMessage($content);
    }

    $duration = $text;
    $durationFormated = number_format($duration);
    if ($durationFormated >= 10 && $durationFormated <= 30) {
        $content = [
            'chat_id' => $chat_id,
            'text' => 'Ваш термін страхування, рокiв - <b>' . $durationFormated . '</b>.',
            'parse_mode' => "html",
            'reply_to_message_id' => $messageId,
            'reply_markup' => $telegram->buildForceReply(),
        ];
        $telegram->sendMessage($content);

        $content = [
            'chat_id' => $chat_id,
            'text' => '👇',
            'parse_mode' => "html",
        ];
        $telegram->sendMessage($content);

        $option = [
            [
                $telegram->buildInlineKeyBoardButton('ГРН', $url = '', $callback_data = 'hrivna'),
                $telegram->buildInlineKeyBoardButton('Доллар США', $url = '', $callback_data = 'dollar'),
            ],
        ];

        $keyb = $telegram->buildInlineKeyBoard($option);
        $content = [
            'chat_id' => $chat_id,
            'text' => '👇 Далi, будьласка, оберiть валюту страховки 👇',
            'parse_mode' => "html",
            'reply_to_message_id' => $messageId,
            'reply_markup' => $keyb,
        ];
        $telegram->sendMessage($content);

    } else {
        $content = [
            'chat_id' => $chat_id,
            'text' => 'Необхідно ввести повне число від 10 до 30',
            'parse_mode' => "html",
            'reply_to_message_id' => $messageId,
            'reply_markup' => $telegram->buildForceReply(),
        ];
        $telegram->sendMessage($content);
    }
}