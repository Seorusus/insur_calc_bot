<?php

include 'Telegram.php';
$botToken = '';
//$botToken = '6105452476:AAG7oUTA6TA7koYsOQ2zmQCO-_76fi3LPFE';
//define( 'API_URL', 'https://api.telegram.org/bot' . $botToken . '/' );
//define("CHAT_ID", "@ins_calc_group");
//define("TG_TOKEN", "6105452476:AAG7oUTA6TA7koYsOQ2zmQCO-_76fi3LPFE");

$telegram = new Telegram($botToken);

/* ================================================== */
/* Writing to message.txt */
function writeLogFile($string, $clear = false){
    $log_file_name = __DIR__."/message.txt";
    $now = date("Y-m-d H:i:s");

    if($clear == false) {
        file_put_contents($log_file_name, $now." ".print_r($string, true)."\r\n", FILE_APPEND);
    }
    else {
        file_put_contents($log_file_name, '');
        file_put_contents($log_file_name, $now." ".print_r($string, true)."\r\n", FILE_APPEND);
    }
}

$data = file_get_contents('php://input');
writeLogFile($data, true);

//echo file_get_contents(__DIR__."/message.txt");


/* ============================================ */

$arrDataAnswer = json_decode($data, true);
$textMessage = mb_strtolower($arrDataAnswer["message"]["text"]);

/* ================================================== */

$chat_id = $telegram->ChatID();
$text = $telegram->Text();

/* ================================================== */
/* для отправки текстовых сообщений */
function TG_sendMessage($getQuery) {
    $ch = curl_init("https://api.telegram.org/bot". TG_TOKEN ."/sendMessage?" . http_build_query($getQuery));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}

/* ================================================== */

function request($method, $params = []) {
    if (!empty($params)) {
        $url =  API_URL . $method . "?" . http_build_query($params);
}
else {
        $url = API_URL . $method;
    }

    return json_decode(file_get_contents($url), JSON_OBJECT_AS_ARRAY);
}

// Send Invite.

$textMessage = 'Вiтаю';

request("sendMessage", [
    'chat_id' => CHAT_ID,
     "text"  	=> $textMessage,
     "parse_mode" => "html",
     'reply_markup' => json_encode([
         'keyboard' => [
             [
                 [
                     'text' => 'Страховий калькулятор',
                     'callback_data' => 'calc_bot'
                 ],
                 [
                     'text' => 'Отримати онлайн консультацию',
                     'callback_data' => 'lonchar'
                 ],
             ],
         ],
         'one_time_keyboard' => false,
         'resize_keyboard' => true,
     ]),
]);


if ($arrDataAnswer["callback_query"]) {
    $dataBut = $arrDataAnswer["callback_query"]["data"];
    $textMessage = mb_strtolower($arrDataAnswer["callback_query"]["message"]["text"]);
    $chatId = $arrDataAnswer["callback_query"]["message"]["chat"]["id"];

    if ($dataBut === "calc_bot") {
        $arrayQuery = array(
            'chat_id' => CHAT_ID,
            'text' => "Ты нажал на 'КНОПКА 1'",
            'parse_mode' => "html",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'insurance_calc_bot',
                            'url' => 'https://t.me/insurance_calc_bot',
                        ],
                    ]
                ],
                'is_persistent' => true,
                'one_time_keyboard' => false,
                'resize_keyboard' => true,
            ]),
        );
        TG_sendMessage($arrayQuery);
    } elseif ($dataBut === "lonchar") {
        $arrayQuery = array(
            'chat_id' => $chatId,
            'text' => "Ты нажал на 'КНОПКА 2'",
            'parse_mode' => "html",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'larisa_lonchar',
                            'url' => 'https://t.me/larisa_lonchar',
                        ],
                    ]
            ],
            'is_persistent' => true,
            'one_time_keyboard' => false,
            'resize_keyboard' => true,
        ]),
        );
        TG_sendMessage($arrayQuery);
    }
}

//if ($callback_data === 'lonchar') {
//    request("sendMessage", [
//        'chat_id' => CHAT_ID,
//        'text' => "Звернутись до експерта Лариси Лончар",
//        'reply_markup' => json_encode([
//            'inline_keyboard' => [
//                [
//                    [
//                        'text' => 'larisa_lonchar',
//                        'url' => 'https://t.me/larisa_lonchar',
//                    ],
//                ]
//            ],
//            'is_persistent' => true,
//            'one_time_keyboard' => false,
//            'resize_keyboard' => true,
//        ]),
//    ]);
//}
//
//request("sendMessage", array(
//    'chat_id' => '@ins_calc_group',
//    'text' => "Приветствую в группе",
////    'disable_web_page_preview' => false,
////    'reply_markup' => json_encode(array('keyboard' => $keyboard)),
//    'reply_markup' => json_encode(array(
//        'inline_keyboard' => array(
//            array(
//                array(
//                    'text' => 'larisa_lonchar',
//                    'url' => 'https://t.me/larisa_lonchar',
//                ),
//                array(
//                    'text' => 'insurance_calc_bot',
//                    'url' => 'tg://insurance_calc_bot',
//                ),
//            )),
//        'is_persistent' => true,
//        'one_time_keyboard' => false,
//        'resize_keyboard' => true,
//    )),
//));
//

/* ================================================== */
if ($text === '/start') {
    if ($data['channel_post']['chat']['type'] === 'channel') {
        $chatId = $data['channel_post']['chat']['id'];
        $reply = 'Working ' . $data['channel_post']['chat']['title'] . $chatId;

        $option = array(
            //First row
            array(
                $telegram->buildKeyboardButton("Button 1", true, true),
                $telegram->buildKeyboardButton("Button 2")),
            //Second row
            array(
                $telegram->buildKeyboardButton("Button 3"),
                $telegram->buildKeyboardButton("Button 4"),
                $telegram->buildKeyboardButton("Button 5")
            ),
            //Third row
            array($telegram->buildKeyboardButton("Button 6"))
        );
        $keyb = $telegram->buildKeyBoard($option, $onetime=true);
        $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => $reply);
        $telegram->sendMessage($content);
    }

    $chat = $data['channel_post']['chat'];
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
}


//
//$chat_id = $telegram->ChatID();
//$text = $telegram->Text();
// $result = $telegram->getData();
// $text = $result['message'] ['text'];
// $content = [
// 	'chat_id' => $chat_id, 'text' => 'Привiт',
// ];
// $telegram->sendMessage($content);

//if($text === '/start') {
//	$option = array(
//    //First row
//    array($telegram->buildKeyboardButton("Button 1", true, true), $telegram->buildKeyboardButton("Button 2")),
//    //Second row
//    array($telegram->buildKeyboardButton("Button 3"), $telegram->buildKeyboardButton("Button 4"), $telegram->buildKeyboardButton("Button 5")),
//    //Third row
//    array($telegram->buildKeyboardButton("Button 6")));
//$keyb = $telegram->buildKeyBoard($option, $onetime=true);
//$content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "This is a Keyboard Test");
//$telegram->sendMessage($content);
//}
