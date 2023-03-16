<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/* Settings */
define("TG_TOKEN", "6105452476:AAG7oUTA6TA7koYsOQ2zmQCO-_76fi3LPFE");
define("TG_USER_ID", "-728206168");
define("CHAT_ID", "@ins_calc_group");

/* для отправки текстовых сообщений */
function TG_sendMessage($getQuery) {
    $ch = curl_init("https://api.telegram.org/bot". TG_TOKEN ."/sendMessage?" . http_build_query($getQuery));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}

/* для отправки изображений */
function TG_sendPhoto($arrayQuery) {
    $ch = curl_init('https://api.telegram.org/bot'. TG_TOKEN .'/sendPhoto');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}

/* для получения данных о файле */
function TG_getFile($arrayQuery) {
    $ch = curl_init("https://api.telegram.org/bot". TG_TOKEN ."/getFile?" . http_build_query($arrayQuery));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}

/* ============================================ */

function list_files($path) {
    if ($path[mb_strlen($path) - 1] != '/') {
        $path .= '/';
    }

    $files = array();
    $dh = opendir($path);
    while (false !== ($file = readdir($dh))) {
        if ($file != '.' && $file != '..' && !is_dir($path.$file) && $file[0] != '.') {
            $files[] = $file;
        }
    }

    closedir($dh);
    return $files;
}

/* ============================================ */

/* Writing to message.txt */
function writeLogFile($string, $clear = false) {
    $log_file_name = __DIR__."/message.txt";
    $now = date("Y-m-d H:i:s");

    if ($clear === 'false') {
        file_put_contents($log_file_name, $now." ".print_r($string, true)."\r\n", FILE_APPEND);
    } else {
        file_put_contents($log_file_name, '');
        file_put_contents($log_file_name, $now." ".print_r($string, true)."\r\n", FILE_APPEND);
    }
}

$data = file_get_contents('php://input');
writeLogFile($data, false);

//echo file_get_contents(__DIR__."/message.txt");

/* ============================================ */

$arrDataAnswer = json_decode($data, true);
$textMessage = mb_strtolower($arrDataAnswer["message"]["text"]);
$chatId = $arrDataAnswer["message"]["chat"]["id"];
var_dump($chatId);

/* ============================================ */

// Send Invite.

$textMessage = '🎯🎯🎯';
if (!$arrDataAnswer['message']) {
    $arrayQuery = array(
        'chat_id'       => CHAT_ID,
        'text'          => $textMessage,
        'parse_mode'    => "html",
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    [
                        'text' => 'Страховий калькулятор',
                    ],
                    [
                        'text' => 'Отримати онлайн консультацию',
                    ],
                ],
            ],
            'is_persistent' => true,
            'one_time_keyboard' => false,
            'resize_keyboard' => true,
        ]),
    );
    TG_sendMessage($arrayQuery);
}




if ($arrDataAnswer['message']) {
    if ($arrDataAnswer['message']['text'] === "Страховий калькулятор") {
        $arrayQuery = array(
            'chat_id' => CHAT_ID,
            'text' => "Перейти до страхового калькулятора",
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
    } elseif ($arrDataAnswer['message']['text'] === "Отримати онлайн консультацию") {
        $arrayQuery = array(
            'chat_id' => $chatId,
            'text' => "Звернутись до експерта Лариси Лончар",
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

/* ================================================== */

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
