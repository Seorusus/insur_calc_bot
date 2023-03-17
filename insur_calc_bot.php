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
                    '📋 Покажи які є страхові програми',
                    '🧮 Давай зробимо розрахунок страхової виплати'
                ],
            ];

            $keyb = $telegram->buildKeyBoard($option, true, true, false);
            $content = [
                'chat_id' => $chat_id,
                'reply_markup' => $keyb,
                'parse_mode' => "html",
                'text' => "<b>Чудово.</b>\n
⬇️⬇️⬇️ Виберайте з чого почнемо ⬇️⬇️⬇️",
                ];
            $telegram->sendMessage($content);
        }

        if (($text === '📋 Покажи які є страхові програми') && (!$telegram->messageFromGroup())) {
                $reply = "<b>Дивiться.</b>\nЦе програми які доступні українцям та діють по всьому світу.\n\nВибирайте групу програм\n⬇️⬇️⬇️";
            // Create option for the custom keyboard. Array of array string
            $option = [
                [
                    '👱‍♀️ Особисте страхування',
                    '👨‍🦳 Пенсійний опціон'
                ],
//                ['👨‍🦳 Пенсійний опціон'],
                [
                    '🩼 Страхування настання ризиків',
                    '👨‍👨‍👦‍👦 Страхування групи осіб'
                ],
//                ['👨‍👨‍👦‍👦 Страхування групи осіб'],
            ];
            // Get the keyboard
            $keyb = $telegram->buildKeyBoard($option, true, true, false);
            $content = [
                'chat_id' => $chat_id,
                'reply_markup' => $keyb,
                'parse_mode' => "html",
                'text' => $reply
            ];
            $telegram->sendMessage($content);
        }

        if (($text === '👱‍♀️ Особисте страхування') && (!$telegram->messageFromGroup())) {
            $reply = "ПРОГРАМИ СТРАХУВАННЯ ЖИТТЯ ФІЗИЧНИХ ОСІБ\n
<b>✅ Надійний страховий захист навіть з мінімальною сумою внеску</b>
З моменту вступу в дію страхового полісу Граве гарантує Вам страховий захист відповідно з обраною програмою, незалежно від суми Вашого внеску.\n
<b>✅ Гарантована сума накопичення</b>
Після закінчення дії договору Граве виплачує Вам гарантовану страхову суму.\n
<b>✅ Додатковий інвестиційний дохід</b>
Вже з другого страхового року крім, усіх гарантованих страхових виплат, Граве додає виплату бонусів від участі в інвестиційному доході компанії.\n
<b>✅ Зручна форма інвестицій</b>
Для зручності Граве пропонує Вам самостійно обрати валюту страхового договору – це можуть бути як долари США, так і українська гривня.\n
<b>✅ Індивідуальний підхід</b>
Граве враховує Ваші індивідуальні особливості та побажання, тому може гнучко комбінувати страхове покриття та зробити його або більш накопичувальним, або більше приділити увагу захисту Вашого життя.\n
<b>✅ Оперативність виплат</b>
Ми робимо все можливе, аби здійснити Вашу страхову виплату впродовж 14 днів з моменту подання всіх необхідних документів.\n
<b>✅ Податкові пільги передбачені законодавством</b>
Зважаючи на те, що договори страхування життя вважаються довгостроковими та накопичувальними, страхувальники мають право на податковий кредит (податок від доходів фізичних осіб, сплачених у вигляді страхових внесків, перераховується та повертається Вам за підсумками року).\n
Програми особистого страхування
⬇️⬇️⬇️";
            // Create option for the custom keyboard. Array of array string
            $option = [
                ['🌱 Програма СЕП - широкий страховий захист'],
                ['Граве Класік - зважена інвестиція коштів'],
                ['Граве Голд - накопичення вагомого капіталу'],
                ['Граве Магістр - накопичення коштів для навчання'],
                ['Граве Медик - фінансова підтримка у разі захворювання'],
                ['Граве Медик - фінансова підтримка у разі захворювання'],
            ];
            // Get the keyboard
            $keyb = $telegram->buildKeyBoard($option, true, true, false);
            $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => $reply, 'parse_mode' => "html",];
            $telegram->sendMessage($content);
        }

        if (($text === '🌱 Програма СЕП - широкий страховий захист') && (!$telegram->messageFromGroup())) {
            $reply = "<b>СТРАХОВА ЕКОНОМІЧНА ПРОГРАМА СЕП</b>
Програма надає можливість не лише забезпечити гідний рівень життя Вам і Вашим рідним, наприклад після виходу на пенсію або у скрутний для Вас час, а й отримати справжній страховий та соціальний захист за всіма видами додаткових тарифів.
<b>СЕП гарантує</b>

Накопичення капіталу
Виплату гарантованої страхової суми разом із накопиченими резервами бонусів після закінчення дії договору.

Унікальний захист життя
• Виплату вигодонабувачам частини гарантованої страхової суми пропорційної часу дії договору страхування разом із накопиченими резервами бонусів. Разом із цим в залежності від розміру страхового платежу виплачується додаткова сума в розмірі 10 000 або 15 000 гривень, у випадку, коли застрахована особа йде з життя внаслідок хвороби. Договір при цьому припиняє свою дію.

• Одночасно виплату вигодонабувачам повної гарантованої страхової суми та частини гарантованої страхової суми пропорційної часу дії договору страхування разом із накопиченими резервами бонусів. Разом із цим в залежності від розміру страхового платежу виплачується додаткова сума в розмірі 10 000 або 15 000 гривень, якщо застрахована особа йде з життя внаслідок нещасного випадку. Договір при цьому припиняє свою дію.

Захист у випадку інвалідності
• Виплату гарантованої страхової суми разом із накопиченими резервами бонусів після закінчення дії договору страхування у разі, якщо протягом дії договору застрахована особа внаслідок нещасного випадку отримує інвалідність 50% і вище. Разом із цим страхувальник звільняється від обов’язку сплачувати чергові страхові платежі.

• Негайну виплату застрахованій особі гарантованої страхової суми та виплату гарантованої страхової суми разом із накопиченими резервами бонусів після закінчення дії договору страхування у разі, якщо протягом дії договору застрахована особа внаслідок нещасного випадку отримує повну 100% інвалідність. Разом із цим страхувальник звільняється від обов’язку сплачувати чергові страхові платежі.

<b><u>Приклад СЕП</u></b>

<i>Застрахована особа:</i>
<b>Жінка віком 30 років</b>
<i>Строк страхування:</i>
<b>20 років</b>
<i>Щорічний страховий платіж:</i>
<b>10 000 грн</b>
<i>Гарантована страхова сума:</i>
<b>214 564 грн</b>
<i>Гарантована страхова сума за додатковим страхуванням ризику смерті</i>
<b>15 000 грн</b>

<b><u>Страхові виплати</u></b>

<b>Виплата у випадку смерті (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
|  2              |   36 456,40 |
|  6              |   84 666,51 |
| 11              |  166 239,74 |
| 16              |  289 563,38 |
</code></pre>
<b>Виплата у разі смерті внаслідок нещасного випадку (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
|  2              |  251 020,40 |
|  6              |  299 230,51 |
| 11              |  380 803,74 |
| 16              |  504 127,38 |
</code></pre>
<b>Виплата по закінченні строку дії договору (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
| 20              |  437 673,33 |
</code></pre>


Звідси Ви можете перейти до індивідуального розрахунку страхової виплати
⬇️⬇️⬇️";
            // Create option for the custom keyboard. Array of array string
            $option = [
                ['🧮 Розрахунок страхової виплати'],
            ];
            // Get the keyboard
            $keyb = $telegram->buildKeyBoard($option, true, true, false);
            $content = [
                'chat_id' => $chat_id,
                'reply_markup' => $keyb,
                'parse_mode' => "html",
                'text' => $reply
            ];
            $telegram->sendMessage($content);
        }
    }
}
/* ================================================== */
