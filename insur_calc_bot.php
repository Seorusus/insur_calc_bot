<?php

include_once 'Telegram.php';

/* If you need to manually take some parameters
*  $result = $telegram->getData();
*  $text = $result["message"] ["text"];
*  $chat_id = $result["message"] ["chat"]["id"];
*/

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/* Settings */
define("TG_TOKEN", "6105452476:AAG7oUTA6TA7koYsOQ2zmQCO-_76fi3LPFE");
define("TG_USER_ID", "-728206168");
define("CHAT_ID", "@ins_calc_group");

// Instances the class
$telegram = new Telegram(TG_TOKEN);

// Take text and chat_id from the message
$text = $telegram->Text();
$chat_id = $telegram->ChatID();
$result = $telegram->getData();

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

    $files = [];
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
if ($telegram->messageFromGroup()) {
    $textMessage = '🎯🎯🎯';
    if (!$arrDataAnswer['message']) {
        $content = [
            'chat_id' => CHAT_ID,
            'text' => $textMessage,
            'parse_mode' => "html",
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
        ];
        $telegram->sendMessage($content);
    }
}

if ($arrDataAnswer['message']) {
    if ($arrDataAnswer['message']['text'] === "Страховий калькулятор") {
        $content = [
            'chat_id' => CHAT_ID,
            'text' =>
                "Бот <b>'Страховий калькулятор'</b> познайомить Вас із програмами австрійської страхової компанії Grawe та допоможе Вам зробити 
<b><i>розрахунок страховки.</i></b>",
            'parse_mode' => "html",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'Перейти до Страхового калькулятора',
                            'url' => 'https://t.me/insurance_calc_bot',
                        ],
                    ]
                ],
                'is_persistent' => true,
                'one_time_keyboard' => false,
                'resize_keyboard' => true,
            ]),
        ];
        $telegram->sendMessage($content);
    } elseif ($arrDataAnswer['message']['text'] === "Отримати онлайн консультацию") {
        $content = [
            'chat_id' => $chatId,
            'text' => "Ви можете безпосередньо поставити запитання щодо страхових програм та отримати безкоштовну консультацію <b>експерта</b>.",
            'parse_mode' => "html",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'Звернутись до експерта Лариси Лончар',
                            'url' => 'https://t.me/larisa_lonchar',
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
}

/* ================================================== */

// Check if the text is a command.
if (!$telegram->messageFromGroup()) {
    if (!is_null($text) && !is_null($chat_id)) {
        if ($text === '/start') {
        $text = $result['message'] ['text'];
            // Shows the Inline Keyboard and Trigger a callback on a button press
            $option = [
                [
                    'Покажи які є страхові програми',
                    'Давай зробимо розрахунок страхової виплати'
                ],
            ];

            $keyb = $telegram->buildKeyBoard($option, true, true, false);
            $content = [
                'chat_id' => $chat_id,
                'reply_markup' => $keyb,
                'parse_mode' => "html",
                'text' => "<b>Чудово</b>,\nВибери з чого почнемо.",
                ];
            $telegram->sendMessage($content);
        }

        if ($text === 'Покажи які є страхові програми') {
            if (!$telegram->messageFromGroup()) {
                $reply = 'Дивись';
            }
            // Create option for the custom keyboard. Array of array string
            $option = [['A', 'B'], ['C', 'D']];
            // Get the keyboard
            $keyb = $telegram->buildKeyBoard($option, true, true, false);
            $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => $reply];
            $telegram->sendMessage($content);
        }


    }
}
/* ================================================== */
