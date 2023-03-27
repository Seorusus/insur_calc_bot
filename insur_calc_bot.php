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
define("INS_CALCULATOR", "🧮 Страховий калькулятор", true);
define("CALL_LORA", "💬 Звернутись до експертки Лариси Лончар", true);
define("CONSULTATION", "💬 Отримати консультацiю експерта", true);
define("CALCULATION", "🧮 Давай зробимо розрахунок страхової виплати", true);
define("CALCULATION_PROG", "🧮 Розрахунок ", true);
define("PERSONAL_INS", "👱‍♀️ Особисте страхування", true);
define("OPSION", "👨‍🦳 Пенсійний опціон", true);
define("SUPPORT_3_HARD", "3️⃣ Страхування важкої хвороби - ГРАВЕ Підтримка 3", true);
define("ACCUMULATION", "💰 Накопичувальна програма - ГРАВЕ Підтримка 3", true);
define("RETURN_GROUP", "⬆️⬆️⬆️ Повернутись до вибору групи програм", true);
define("RETURN_LIST_HELTH_PROT", "⬇️⬇️⬇️ Повернутись до списку програм Захисту здоров’я", true);
define("SHOW_INS_PROGRAMS", "📋 Покажи які є страхові програми", true);
define("SEP", "🌱 Граве СЕП - широкий страховий захист", true);
define("CLASSIC", "🏛 Граве Класік - зважена інвестиція коштів", true);
define("GOLD", "🥇 Граве Голд - накопичення вагомого капіталу", true);
define("INVEST_PLAN", "🗓 ІНВЕСТ План - для майбутніх значних витрат або додаткової пенсії", true);
define("UNIOR_EXTRA", "🤱 ЮНІОР Екстра - Захист годувальника та дитини", true);
define("MAGISTR", "👨🏻‍🎓 Граве Магістр - накопичення коштів для навчання", true);
define("UNIVERSAL", "🌐 ГРАВЕ Універсал - Можливість вибору ступеня захисту", true);
define("GRAVE_MEDIC", "❤️‍🔥 ГРАВЕ Медик - Захист за двома програмами страхування", true);
define("HEALTHY_PROTECT", "🪴 Захист здоров’я", true);
define("GROUP_INS", "👨‍👨‍👦‍👦 Страхування групи осіб", true);
define("SUPPORT_HEALTH_1", "👨‍🍼 ГРАВЕ Підтримка - Захист здоров'я годувальника та дитини", true);
define("SUPPORT_HEALTH_3", "3️⃣ Граве Підтримка 3 - Захист на випадок діагностування важких хвороб", true);
define("OTHER_PERS_PROGRAMS", "⬇️⬇️⬇️ Iншi програми Особистого страхування життя", true);
define("GRAVE_MEDIC_ACCUMULATION", "➕ ГРАВЕ Медик - Накопичувальна програма", true);
define("GRAVE_MEDIC_RISK_INS", "❗ ГРАВЕ Медик - Страхування настання ризиків", true);


define("TG_TOKEN", "6105452476:AAG7oUTA6TA7koYsOQ2zmQCO-_76fi3LPFE", true);
define("TG_USER_ID", "-728206168", true);
define("CHAT_ID", "@ins_calc_group", true);

// Instances the class
$telegram = new Telegram(TG_TOKEN);

// Take text and chat_id from the message
$text = $telegram->Text();
$chat_id = $telegram->ChatID();
$result = $telegram->getData();
$callback_query = $telegram->Callback_Query();

/* для отправки изображений */
function tgSendPhoto($arrayQuery) {
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
function tgGetFile($arrayQuery) {
    $ch = curl_init("https://api.telegram.org/bot". TG_TOKEN ."/getFile?" . http_build_query($arrayQuery));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}

/* ============================================ */

function listFiles($path) {
    if ($path[mb_strlen($path) - 1] !== '/') {
        $path .= '/';
    }

    $files = [];
    $dh = opendir($path);
    while (false !== ($file = readdir($dh))) {
        if ($file !== '.' && $file !== '..' && !is_dir($path.$file) && $file[0] !== '.') {
            $files[] = $file;
        }
    }

    closedir($dh);
    return $files;
}

/* ============================================ */

/* Writing to message.txt */
function writeLogFile($string, $clear = false) {
    $logFileName = __DIR__."/message.json";
    $now = date("Y-m-d H:i:s");

    if ($clear === 'false') {
        file_put_contents($logFileName, $now." ".print_r($string, true)."\r\n", FILE_APPEND);
    } else {
        file_put_contents($logFileName, '');
        file_put_contents($logFileName, $now." ".print_r($string, true)."\r\n", FILE_APPEND);
    }
}

$data = file_get_contents('php://input');
writeLogFile($data, true);

echo file_get_contents(__DIR__."/message.json");

/* ============================================ */

$arrDataAnswer = json_decode($data, true);
$textMessage = mb_strtolower($arrDataAnswer["message"]["text"]);
$chatId = $arrDataAnswer["message"]["chat"]["id"];

/* ============================================ */
//$test = $data['channel_post']['chat']['type'];

// Send Invite.
if ($telegram->messageFromGroup() || ($data['channel_post']['chat']['type'] !== 'channel')) {
    if (is_null($chat_id)) {
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
                                'text' => INS_CALCULATOR,
                            ],
                            [
                                'text' => CONSULTATION,
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
}

if ($arrDataAnswer['message']) {
    if ($text === INS_CALCULATOR) {
        $content = [
            'chat_id' => CHAT_ID,
            'text' =>
                "Бот <b>" . INS_CALCULATOR . "</b> познайомить Вас із програмами австрійської страхової компанії Grawe та допоможе Вам зробити 
<b><i>розрахунок страховки.</i></b>",
            'parse_mode' => "html",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => INS_CALCULATOR,
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
    } elseif ($text === CONSULTATION) {
        $content = [
            'chat_id' => $chatId,
            'text' => "Ви можете безпосередньо поставити запитання щодо страхових програм та отримати безкоштовну консультацію <b>експерта</b>.",
            'parse_mode' => "html",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => CALL_LORA,
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

if (!empty($callback_query['data'])) {
    if ($callback_query['data'] === 'back_menu') {
        $option = [
            [
                SHOW_INS_PROGRAMS,
                CALCULATION,
            ],
        ];

        $keyb = $telegram->buildKeyBoard($option, true, true, false);
        $name = $telegram->FirstName();
        $content = [
            'chat_id' => $chat_id,
            'reply_markup' => $keyb,
            'parse_mode' => "html",
            'text' => "Добре, $name, давайте з початку)",
        ];
        $telegram->sendMessage($content);

    }

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
            'reply_markup' => $telegram->buildForceReply(),
        ];
        $telegram->sendMessage($content);

        $content = [
            'chat_id' => $chat_id,
            'text' => '👇 Далi введіть вік на початку страхування. 👇
<i>* Мінімальний вік для страхування – 15 років.
* Максiмальний вік для страхування – 60 років.</i>',
            'parse_mode' => "html",
            'reply_to_message_id' => $messageId,
            'reply_markup' => $telegram->buildForceReply(),
        ];


//        $option = [
//            [
//                $telegram->buildInlineKeyBoardButton(
//                    '👇 Далi введіть вік на початку страхування. 👇',
//                    $url = '',
//                    $callback_data = 'age'),
//            ],
//        ];
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

/* ================================================== */

// Check if the text is a command.
if (!$telegram->messageFromGroup()) {
    if (!is_null($text) && !is_null($chat_id)) {

        switch ($text) {
            case '/start':
            case '/back':
                $text = $result['message']['text'];
                // Shows the Inline Keyboard and Trigger a callback on a button press
                $option = [
                    [
                        SHOW_INS_PROGRAMS,
                        CALCULATION,
                    ],
                ];

                $keyb = $telegram->buildKeyBoard($option, true, true, false);
                $name = $telegram->FirstName();
                $content = [
                    'chat_id' => $chat_id,
                    'reply_markup' => $keyb,
                    'parse_mode' => "html",
                    'text' => "Привiт, $name!
Приємно познайомитись. Сьогоднi " . date('d-m-Y', $telegram->Date()) . "
                
⬇️⬇️⬇️ Виберайте з чого почнемо ⬇️⬇️⬇️",
                ];
                $telegram->sendMessage($content);
                break;

            case CALCULATION:
                if (!$telegram->messageFromGroup()) {
//                    $reply = "<b>Оберiть, будьласка, для якої проргами робимо розрахунок</b>\n⬇️⬇️⬇️";
                    $reply = "<b>Виберiть програму для розрахунку</b>";
                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [CALCULATION_PROG . " " . SEP],
                        [CALCULATION_PROG . " " . CLASSIC],
                        [CALCULATION_PROG . " " . GOLD],
                        [CALCULATION_PROG . " " . INVEST_PLAN],
                        [CALCULATION_PROG . " " . UNIOR_EXTRA],
                        [CALCULATION_PROG . " " . MAGISTR],
                        [CALCULATION_PROG . " " . UNIVERSAL],
                        [CALCULATION_PROG . " " . SUPPORT_HEALTH_1],
                        [CALCULATION_PROG . " " . GRAVE_MEDIC_ACCUMULATION],
                        [CALCULATION_PROG . " " . ACCUMULATION],
                        [CALCULATION_PROG . " " . SUPPORT_3_HARD],
                        [CALCULATION_PROG . " " . GRAVE_MEDIC_RISK_INS],
                        [CALCULATION_PROG . " " . ACCUMULATION],
                        [CONSULTATION],
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
                break;

            case SHOW_INS_PROGRAMS:
            case RETURN_GROUP:
                if (!$telegram->messageFromGroup()) {
                    $reply = "<b>Дивiться.</b>\nЦе програми які доступні українцям та діють по всьому світу.\n\nОбирайте групу програм\n⬇️⬇️⬇️";
                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [
                            PERSONAL_INS,
                            OPSION,
                        ],
                        [
                            HEALTHY_PROTECT,
                            GROUP_INS,
                        ],
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
                break;

            case PERSONAL_INS:
                if (!$telegram->messageFromGroup()) {
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
                        [SEP],
                        [CLASSIC],
                        [GOLD],
                        [INVEST_PLAN],
                        [UNIOR_EXTRA],
                        [MAGISTR],
                        [UNIVERSAL],
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
                }
                break;

            case GROUP_INS:
                if (!$telegram->messageFromGroup()) {
                    $reply = "КОРПОРАТИВНЕ СТРАХУВАННЯ <b>СТРАХУВАННЯ ГРУПИ ОСІБ</b>\n
Програми корпоративного страхування – ефективний мотиваційний інструмент в руках керівника підприємства. І незалежно від того, що є вирішальним в укладенні накопичувального договору страхування на користь працівників – бажання підвищити імідж компанії в очах команди чи турбота про благополуччя персоналу; у будь-якому випадку і роботодавець, і застрахований працівник отримують чимало вигідних переваг.

Програми корпоративного страхування життя забезпечують:

<b><u>Соціальний захист</u></b>
• захист життя працівників Вашого підприємства;
• надійне накопичення коштів для недержавної пенсії Вашого працівника;
• матеріальну допомогу сім’ї Вашого застрахованого працівника внаслідок нещасного випадку;

<b><u>Інвестиційний дохід</u></b>
виплата бонусів від участі в інвестиційному доході компанії

<b><u>Додаткову мотивацію персоналу</u></b>
• впевненість у майбутньому;
• матеріальне заохочення персоналу;
• лояльність до роботодавця;
• позитивний імідж соціально відповідального роботодавця;

Наші програми дуже гнучкі, і ми можемо комбінувати страховий захист залежно від Ваших побажань та особливостей роботи підприємства. Будь ласка, не соромтесь звертатися до наших спеціалістів, вони допоможуть обрати найбільш вигідну пропозицію для страхування Ваших працівників.";
                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [RETURN_GROUP],
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
                }
                break;

            case OPSION:
                if (!$telegram->messageFromGroup()) {
                    $reply = "ПЕНСІЙНИЙ ОПЦІОН <b>НЕДЕРЖАВНЕ ПЕНСІЙНЕ ЗАБЕЗПЕЧЕННЯ</b>\n
<b>✅ По закінченні строку дії договору страхування замість одноразової страхової виплати може бути обрана виплата у формі пенсійного опціону (ануїтету).</b>

Пенсійний опціон (ануїтет)
Послідовність страхових виплат, які здійснює Страховик Вигодонабувачеві рівними частинами довічно або протягом терміну, визначеного договором страхування. 
Виплата ануїтету здійснюється в розмірі, у строки та на умовах, визначених сторонами договору страхування й оформлених додатковою угодою (або додатком) до договору страхування. 

Договір страхування може передбачати такі види ануїтету: 
• ануїтет на визначений строк; 
• довічний ануїтет. 
Після здійснення першої виплати у формі ануїтету вид ануїтету не може бути змінено.

Ануїтет на визначений строк означає, що Застрахована особа отримуватиме від страхової компанії певні грошові суми раз на місяць, квартал, півріччя, рік (на вибір) протягом визначеного строку (10, 15, 20 і більше років). 

Довічна пенсія (довічний ануїтет) – виплати, які здійснюються Страховиком один раз на місяць, квартал, півріччя, рік (на вибір) протягом життя Застрахованої особи. 

До ануїтету на визначений строк та довічної пенсії можуть бути додані додаткові опції, а саме: 
• опція із захистом капіталу; 
• опція переходу виплат до іншої особи (пенсійний опціон з правом успадкування). 

Ануїтет на визначений строк із захистом капіталу гарантує, що Застрахована особа (одержувач ануїтету) та її Вигодонабувач отримають повну суму сплачених премій у будь-якому випадку, як у випадку дожиття Застрахованої особи (одержувача ануїтету) до закінчення визначеного строку виплат, так і у випадку її смерті протягом цього строку. У разі смерті одержувача ануїтету протягом визначеного строку виплат Вигодонабувачеві виплачується різниця між інвестованим капіталом (страховою виплатою по закінченні дії договору страхування) та всіма виплатами у формі ануїтету, які були сплачені одержувачеві ануїтету до моменту його смерті. Ця різниця не може бути більше за загальну суму всіх належних до сплати пенсійних виплат і підлягає виплаті Вигодонабувачеві при умові, що розмір отриманих одержувачем ануїтету пенсійних виплат не перевищує розміру інвестованого капіталу. 
Ануїтет на визначений строк з опцією переходу виплат до іншої особи гарантує, що у випадку смерті Застрахованої особи (одержувача ануїтету) протягом визначеного строку виплат іншій Застрахованій особі (одержувачу ануїтету) триватимуть виплати ануїтету, за умови, що та жива на момент смерті першого одержувача ануїтету. Виплати іншому одержувачеві ануїтету припиняються або у разі його смерті, або у разі закінчення визначеного строку виплат ануїтету. 

Компанія «ГРАВЕ УКРАЇНА Страхування життя» пропонує такі види довічних ануїтетів: 
• довічний ануїтет із захистом капіталу; 
• довічний ануїтет з опцією переходу виплат до іншої особи. 

Довічний ануїтет із захистом капіталу гарантує, що Застрахована особа (одержувач ануїтету) та її Вигодонабувач отримають повну суму сплачених премій у будь-якому випадку. У разі смерті одержувача ануїтету Вигодонабувачеві виплачується різниця між інвестованим капіталом (страховою виплатою по закінченні дії договору страхування) та всіма виплатами у формі ануїтету, які були сплачені одержувачу ануїтету до моменту його смерті. Ця різниця не може бути більше за загальну суму всіх належних до сплати пенсійних виплат і підлягає виплаті Вигодонабувачеві за умови, що розмір отриманих одержувачем ануїтету пенсійних виплат не перевищує розміру інвестованого капіталу. 
Довічний ануїтет із опцією переходу виплат до іншої особи гарантує, що у випадку смерті Застрахованої особи (одержувача ануїтету) триватимуть виплати ануїтету іншій Застрахованій особі (одержувачу ануїтету), за умови, що та жива на момент смерті першого одержувача ануїтету. Виплати іншому одержувачеві ануїтету припиняються у разі його смерті.
";
                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [RETURN_GROUP],
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
                }
                break;

            case HEALTHY_PROTECT:
                if (!$telegram->messageFromGroup()) {
                    $reply = "<b>ЗАХИСТ ЗДОРОВ'Я</b>\n
Сучасна медицина зробила величезний крок вперед.
І зараз нікого не дивує той факт, що лікуванню піддаються більшість захворювань, які ще 20 років тому здавалися невиліковними. 

Проте прогресивні методи лікування потребують значних фінансових вкладень. Нажаль саме матеріальна сторона питання найчастіше вирішує якість життя після одужання. Щоб не витрачати час на пошук джерел фінансування, необхідних для лікування захворювання, варто заздалегідь подбати про своє здоров’я та здоров’я своїх дітей. 

З цією метою компанія «ГРАВЕ УКРАЇНА Страхування життя» розробила три нові страхові програми, які об'єднують в собі покриття медичних ризиків і накопичення грошових коштів, захист заощаджень: «ГРАВЕ Медик», «ГРАВЕ Підтримка» та «ГРАВЕ Підтримка 3».";
                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [GRAVE_MEDIC],
                        [SUPPORT_HEALTH_1],
                        [SUPPORT_HEALTH_3],
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
                }
                break;

            case SEP:
                if (!$telegram->messageFromGroup()) {
                    $reply = "<b>СТРАХОВА ЕКОНОМІЧНА ПРОГРАМА <u>СЕП</u></b>

Програма надає можливість не лише забезпечити гідний рівень життя Вам і Вашим рідним, наприклад після виходу на пенсію або у скрутний для Вас час, а й отримати справжній страховий та соціальний захист за всіма видами додаткових тарифів.

<u><b>СЕП гарантує</b></u>

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
Програми СЕП  ⬇️⬇️⬇️";
                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [CALCULATION_PROG . " " . SEP],
                        [OTHER_PERS_PROGRAMS],
                        [CONSULTATION],
                    ];
                    // Get the keyboard
                    $keyb = $telegram->buildKeyBoard($option, true, true, false);
                    $content = [
                        'chat_id' => $chat_id,
                        'reply_markup' => $keyb,
                        'parse_mode' => "html",
                        'protect_content' => 'true',
                        'text' => $reply,
                    ];
                    $telegram->sendMessage($content);
                }
                break;

            case CLASSIC:
                if (!$telegram->messageFromGroup()) {
                    $reply = "<b>МАКСИМАЛЬНИЙ ЗАХИСТ ЖИТТЯ <u>ГРАВЕ Класик</u></b>

Програма спрямована на задоволення потреб, пов’язаних із максимальним захистом життя у випадках настання непередбачуваних життєвих обставин, оскільки перед- бачає виплати в разі настання тривалої інвалідності внаслідок нещасного випадку та подвійну виплату страхової суми в разі смерті застрахованої особи внаслідок нещасного випадку.

<u><b>ГРАВЕ КЛАСИК гарантує</b></u>

Накопичення, збереження та примноження коштів
Виплату гарантованої страхової суми разом із накопиченими резервами бонусів після закінчення дії договору страхування.

Захист життя
Виплату вигодонабувачам гарантованої страхової суми разом із накопиченими резервами бонусів, у випадку, коли застрахована особа йде з життя.

Подвійний захист життя
Виплату вигодонабувачам подвійної гарантованої страхової суми разом із накопиченими резервами бонусів, у випадку, коли застрахована особа йде з життя внаслідок нещасного випадку.

Захист у випадку інвалідності
• Виплату гарантованої страхової суми разом із накопиченими резервами бонусів після закінчення дії договору страхування в разі, якщо протягом дії договору застрахована особа внаслідок нещасного випадку отримує інвалідність 50% і вище. Разом із цим страхувальник звільняється від обов’язку сплачувати чергові страхові платежі.

• Негайну виплату застрахованій особі гарантованої страхової суми та виплату гарантованої страхової суми разом із накопиченими резервами бонусів після закінчення дії договору страхування в разі, якщо протягом дії договору застрахована особа внаслідок нещасного випадку отримує повну 100% інвалідність. Разом із цим страхувальник звільняється від обов’язку сплачувати чергові страхові платежі.

<b><u>Приклад ГРАВЕ КЛАСИК</u></b>

<i>Застрахована особа:</i>
<b>Жінка віком 30 років</b>
<i>Строк страхування:</i>
<b>20 років</b>
<i>Щорічний страховий платіж:</i>
<b>10 000 грн</b>
<i>Гарантована страхова сума:</i>
<b>199 601 грн</b>

<b><u>Страхові виплати</u></b>

<b>Виплата у випадку смерті (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
|  2              |  199 601,00 |
|  6              |  204 398,62 |
| 11              |  229 883,60 |
| 16              |  293 753,65 |
</code></pre>
<b>Виплата у разі смерті внаслідок нещасного випадку (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
|  2              |  399 202,00 |
|  6              |  403 999,62 |
| 11              |  429 484,60 |
| 16              |  493 354,65 |
</code></pre>
<b>Виплата по закінченні строку дії договору (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
| 20              |  404 513,46 |
</code></pre>


Звідси Ви можете перейти до індивідуального розрахунку страхової виплати
Програми ГРАВЕ КЛАСИК   ⬇️⬇️⬇️";
                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [CALCULATION_PROG . " " . CLASSIC],
                        [OTHER_PERS_PROGRAMS],
                        [CONSULTATION],
                    ];
                    // Get the keyboard
                    $keyb = $telegram->buildKeyBoard($option, true, true, false);
                    $content = [
                        'chat_id' => $chat_id,
                        'reply_markup' => $keyb,
                        'parse_mode' => "html",
                        'protect_content' => 'true',
                        'text' => $reply,
                    ];
                    $telegram->sendMessage($content);
                }
                break;

            case GOLD:
                if (!$telegram->messageFromGroup()) {
                    $reply = "<b>НАКОПИЧЕННЯ ПРОТЯГОМ 10 РОКІВ <u>ГРАВЕ Голд</u></b>

Накопичення вагомого капіталу до Вашого 85- річчя та захист життя з можливістю залишення вагомого спадку дітям та онукам і зі строком сплати страхового платежу впродовж лише 10 років.

<u><b>ГРАВЕ Голд гарантує</b></u>

Накопичення коштів до 85-річчя
Виплату застрахованій особі гарантованої страхової суми разом із накопиченими резервами бонусів по досягненню нею 85 років.

Накопичення коштів у спадок
Виплату вигодонабувачам гарантованої страхової суми разом із накопиченими резервами бонусів, якщо застрахована особа йде з життя до досягнення 85-річного віку. За бажанням клієнта, вигодонабувачем може бути призначена юридична особа, яка організує ритуальну церемонію.

<b><u>Приклад ГРАВЕ Голд</u></b>

<i>Застрахована особа:</i>
<b>Чоловік віком 40 років</b>
<i>Строк страхування:</i>
<b>45 років</b>
<i>Строк сплати страхового платежу:</i>
<b>10 років</b>
<i>Щорічний страховий платіж:</i>
<b>10 000 грн</b>
<i>Гарантована страхова сума:</i>
<b>143 472 грн</b>

<b><u>Страхові виплати</u></b>

<b>Виплата у випадку смерті (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
|  2              |  143 472,00 |
| 15              |  214 931,07 |
| 25              |  434 947,79 |
| 35              |1 050 805,71 |
</code></pre>
<b>Виплата по досягненні 85 років (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
| 45              |2 752 897,62 |
</code></pre>


Звідси Ви можете перейти до індивідуального розрахунку страхової виплати
Програми ГРАВЕ Голд   ⬇️⬇️⬇️";
                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [CALCULATION_PROG . " " . GOLD],
                        [OTHER_PERS_PROGRAMS],
                        [CONSULTATION],
                    ];
                    // Get the keyboard
                    $keyb = $telegram->buildKeyBoard($option, true, true, false);
                    $content = [
                        'chat_id' => $chat_id,
                        'reply_markup' => $keyb,
                        'parse_mode' => "html",
                        'protect_content' => 'true',
                        'text' => $reply,
                    ];
                    $telegram->sendMessage($content);
                }
                break;

            case INVEST_PLAN:
                if (!$telegram->messageFromGroup()) {
                    $reply = "<b>НАКОПИЧЕННЯ ДОСТАТНЬОГО КАПІТАЛУ <u>ІНВЕСТ План</u></b>

Програма, яка допоможе поступово, протягом тривалого часу накопичити достатній капітал для фінансового забезпечення майбутніх значних витрат або додаткової пенсії. Програма не передбачає підкріплення жодного тарифу додаткового страхування.
<u><b>ІНВЕСТ План гарантує</b></u>

Накопичення капіталу
Виплату гарантованої страхової суми разом із накопиченими резервами бонусів після закінчення дії договору. 

Захист життя
Виплату вигодонабувачам частини гарантованої страхової суми пропорційної часу дії договору страхування разом із накопиченими резервами бонусів.

<b><u>Приклад ІНВЕСТ План</u></b>

<i>Застрахована особа:</i>
<b>Жінка віком 30 років</b>
<i>Строк страхування:</i>
<b>20 років</b>
<i>Щорічний страховий платіж:</i>
<b>10 000 грн</b>
<i>Гарантована страхова сума:</i>
<b>250 626 грн</b>

<b><u>Страхові виплати</u></b>

<b>Виплата у випадку смерті (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
|  2              |   25 062,60 |
|  6              |   81 375,43 |
| 11              |  176 658,74 |
| 16              |  320 709,49 |
</code></pre>
<b>Виплата по досягненні 85 років (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
| 20              |  511 233,48 |
</code></pre>


Звідси Ви можете перейти до індивідуального розрахунку страхової виплати
Програми ІНВЕСТ План   ⬇️⬇️⬇️";
                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [CALCULATION_PROG . " " . INVEST_PLAN],
                        [CONSULTATION],
                    ];
                    // Get the keyboard
                    $keyb = $telegram->buildKeyBoard($option, true, true, false);
                    $content = [
                        'chat_id' => $chat_id,
                        'reply_markup' => $keyb,
                        'parse_mode' => "html",
                        'protect_content' => 'true',
                        'text' => $reply,
                    ];
                    $telegram->sendMessage($content);
                }
                break;

            case UNIOR_EXTRA:
                if (!$telegram->messageFromGroup()) {
                    $reply = "<b>ЗАХИСТ ГОДУВАЛЬНИКА ТА ДИТИНИ <u>ЮНІОР Екстра</u></b>

Класична європейська програма, разом із якою Ваша дитина отримує захист на випадок втрати годувальника, а Ви — власні гарантії, що капітал для забезпечення майбутнього Вашої дитини буде створено вчасно.

<u><b>ЮНІОР ЕКСТРА гарантує</b></u>

Накопичення коштів дитині
Виплату дитині гарантованої страхової суми разом із накопиченими резервами бонусів після закінчення дії договору страхування.

Захист у випадку смерті годувальника
Виплату дитині гарантованої страхової суми разом із накопиченими резервами бонусів у кінці дії договору страхування, у випадку, коли застрахована особа (годувальник) йде з життя (в тому числі, з причини природної смерті). При цьому протягом дії договору існує можливість отримати дисконтовану гарантовану страхову суму.

Захист у випадку інвалідності
Виплату дитині гарантованої страхової суми, у разі, якщо протягом дії договору вона отримала повну 100% інвалідність внаслідок нещасного випадку. Разом із цим договір продовжує свою дію.

ПАКЕТ «НЕЩАСНИЙ ВИПАДОК» може бути придбаний за бажанням застрахованої особи (годувальника) разом із головною програмою.

Пакет «Нещасний випадок» — це додатковий захист із страховою сумою 25 000 або 50 000 гривень, умови якого передбачають:
• Додатковий захист внаслідок нещасного випадку, якщо застрахований (годувальник) йде з життя.
• Додатковий захист застрахованої особи (годувальника) у разі настання інвалідності внаслідок нещасного випадку.

Пакет «Нещасний випадок» гарантує
• Негайну виплату дитині страхової суми за пакетом «Нещасний випадок» та виплату дитині гарантованої страхової суми за головною програмою разом із накопиченими резервами бонусів після закінчення дії договору страхування, якщо протягом дії договору застрахована особа (годувальник) йде з життя внаслідок нещасного випадку.
• Виплату застрахованій особі (годувальнику) страхової суми за пакетом «Нещасний випадок» у разі, якщо протягом дії договору вона отримала повну 100% інвалідність внаслідок нещасного випадку.

<b><u>Приклад ЮНІОР ЕКСТРА</u></b>
<i>Приклад наведений у випадку смерті застрахованої особи впродовж першого року страхування.</i>

<i>Застраховані особи:</i>
<b>Жінка віком 30 років,
дитина віком 5 років</b>
<i>Строк страхування:</i>
<b>20 років</b>
<i>Щорічний страховий платіж:</i>
<i>за головною програмою:</i>
<b>10 000 грн</b>
<i>за пакетом «Нещасний випадок»</i>
<b>150 грн</b>
<i>Гарантована страхова сума за головною програмою:</i>
<b>223 714 грн</b>
<i>Гарантована страхова сума за пакетом «Нещасний випадок»</i>
<b>50 000 грн</b>

<b><u>Страхові виплати</u></b>

<b>Виплата у випадку смерті дисконтованої страхової суми (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
|  2              |  127 978,91 |
|  6              |  186 930,99 |
| 11              |  301 204,23 |
| 16              |  486 233,60 |
</code></pre>
<b>Виплата у разі смерті внаслідок нещасного випадку (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
|  2              |  177 978,91 |
|  6              |  236 930,99 |
| 11              |  351 204,23 |
| 16              |  536 233,60 |
</code></pre>
<b>Виплата по закінченні строку дії договору (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
| 20              |  454 705,19 |
</code></pre>


Звідси Ви можете перейти до індивідуального розрахунку страхової виплати
Програми ЮНІОР ЕКСТРА  ⬇️⬇️⬇️";
                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [CALCULATION_PROG . " " . UNIOR_EXTRA],
                        [OTHER_PERS_PROGRAMS],
                        [CONSULTATION],
                    ];
                    // Get the keyboard
                    $keyb = $telegram->buildKeyBoard($option, true, true, false);
                    $content = [
                        'chat_id' => $chat_id,
                        'reply_markup' => $keyb,
                        'parse_mode' => "html",
                        'protect_content' => 'true',
                        'text' => $reply,
                    ];
                    $telegram->sendMessage($content);
                }
                break;

            case MAGISTR:
                if (!$telegram->messageFromGroup()) {
                    $reply = "<b>НАКОПИЧЕННЯ КОШТІВ НА НАВЧАННЯ ДИТИНІ <u>ГРАВЕ Магістр</u></b>

Найбільше накопичення коштів дитині на освіту та страховий захист двох осіб: годувальника і дитини. Характерною рисою програми є те, що дитина отримує максимальний страховий захист на випадок втрати годувальника внаслідок нещасного випадку.

<u><b>ГРАВЕ Магістр гарантує</b></u>

Накопичення коштів на навчання

Виплату дитині гарантованої страхової суми разом із накопиченими резервами бонусів після закінчення дії договору страхування.

 

Захист у випадку смерті годувальника

• Виплату дитині частини гарантованої страхової суми, пропорційної часу дії договору страхування, разом із накопиченими резервами бонусів у випадку, коли застрахована особа (годувальник) йде з життя внаслідок хвороби. Договір при цьому припиняє свою дію.

• Одночасно виплату дитині повної гарантованої страхової суми та частини гарантованої страхової суми, пропорційної часу дії договору страхування, разом із накопиченими резервами бонусів у разі, коли застрахована особа (годувальник) йде з життя внаслідок нещасного випадку. Договір при цьому припиняє свою дію.

 

Захист у випадку інвалідності

• Виплату дитині гарантованої страхової суми разом із накопиченими резервами бонусів після закінчення дії договору страхування у разі, якщо протягом дії договору застрахована особа (годувальник) внаслідок нещасного випадку отримує інвалідність 50% і вище. Разом із цим страхувальник звільняється від обов’язку сплачувати чергові страхові платежі.

• Негайну виплату застрахованій особі (годувальнику) гарантованої страхової суми та виплату дитині гарантованої страхової суми разом із накопиченими резервами бонусів після закінчення дії договору страхування у разі, якщо протягом дії договору застрахована особа (годувальник) внаслідок нещасного випадку отримує повну 100% інвалідність. Разом із цим страхувальник звільняється від обов’язку сплачувати чергові страхові платежі.

• Виплату дитині гарантованої страхової суми у разі, якщо протягом дії договору вона внаслідок нещасного випадку отримала повну інвалідність. Разом із цим договір продовжує свою дію.
<b><u>Приклад ГРАВЕ Магістр</u></b>
<i>Приклад наведений у випадку смерті застрахованої особи впродовж першого року страхування.</i>

<i>Застраховані особи:</i>
<b>Жінка віком 30 років,
дитина віком 1 рiк</b>
<i>Строк страхування:</i>
<b>20 років</b>
<i>Щорічний страховий платіж:</i>
<b>10 000 грн</b>
<i>Гарантована страхова сума:</i>
<b>213 220 грн</b>

<b><u>Страхові виплати</u></b>

<b>Виплата у випадку смерті (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
|  2              |   21 322,00 |
|  6              |   69 230,12 |
| 11              |  150 292,36 |
| 16              |  272 843,50 |
</code></pre>
<b>Виплата у разі смерті внаслідок нещасного випадку (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
|  2              |  234 542,00 |
|  6              |  282 450,12 |
| 11              |  363 512,36 |
| 16              |  486 063,50 |
</code></pre>
<b>Виплата по закінченні строку дії договору (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
| 20              |  434 931,73 |
</code></pre>


Звідси Ви можете перейти до індивідуального розрахунку страхової виплати
Програми ГРАВЕ Магістр  ⬇️⬇️⬇️";
                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [CALCULATION_PROG . " " . MAGISTR],
                        [OTHER_PERS_PROGRAMS],
                        [CONSULTATION],
                    ];
                    // Get the keyboard
                    $keyb = $telegram->buildKeyBoard($option, true, true, false);
                    $content = [
                        'chat_id' => $chat_id,
                        'reply_markup' => $keyb,
                        'parse_mode' => "html",
                        'protect_content' => 'true',
                        'text' => $reply,
                    ];
                    $telegram->sendMessage($content);
                }
                break;

            case UNIVERSAL:
                if (!$telegram->messageFromGroup()) {
                    $reply = "<b>ВИБІР СТУПЕНЯ ЗАХИСТУ <u>ГРАВЕ Універсал</u></b>

Ви відчуваєте брак коштів? З програмою ГРАВЕ Універсал Ви почнете поступово накопичувати кошти, жити впевнено і не залежати від забаганок долі. ГРАВЕ Універсал — максимально ефективне накопичення капіталу впродовж визначеного терміну дії договору з можливістю вибору ступеня захисту.

<u><b>ГРАВЕ Універсал гарантує</b></u>

Максимально ефективне накопичення капіталу
Виплату гарантованої страхової суми разом із накопиченими резервами бонусів після закінчення дії договору страхування.

Надійний захист життя
Виплату вигодонабувачам гарантованої страхової суми разом із накопиченими резервами бонусів після закінчення дії договору, у випадку, коли застрахована особа йде з життя. При цьому протягом дії договору існує можливість отримати дисконтовану гарантовану страхову суму.

ПАКЕТ «НЕЩАСНИЙ ВИПАДОК» може бути придбаний за бажанням страхувальника разом із головною програмою.

Пакет «Нещасний випадок»— це додатковий захист із страховою сумою 25 000 або 50 000 гривень, умови якого передбачають:
• Додатковий захист внаслідок нещасного випадку, якщо застрахований йде з життя.
• Додатковий захист застрахованої особи у разі настання інвалідності внаслідок нещасного випадку.

Пакет «Нещасний випадок» гарантує
• Негайну виплату вигодонабувачам страхової суми за пакетом «Нещасний випадок» та виплату гарантованої страхової суми за головною програмою разом із накопиченими резервами бонусів після закінчення дії договору страхування, якщо протягом дії договору застрахована особа йде з життя внаслідок нещасного випадку.
• Виплату застрахованій особі страхової суми за пакетом «Нещасний випадок» у разі, якщо протягом дії договору вона внаслідок нещасного випадку отримала повну 100% інвалідність.

<b><u>Приклад ГРАВЕ Універсал</u></b>
<i>Приклад наведений у випадку смерті застрахованої особи впродовж першого року страхування.</i>

<i>Застраховані особи:</i>
<b>Жінка віком 30 років</b>
<i>Строк страхування:</i>
<b>20 років</b>
<i>Щорічний страховий платіж за головною програмою</i>
<b>10 000 грн</b>
<i>Щорічний страховий платіж за за пакетом «Нещасний випадок»</i>
<b>150 грн</b>
<i>Гарантована страхова сума за головною програмою:</i>
<b>228 833 грн</b>
<i>Гарантована страхова сума за пакетом «Нещасний випадок»:</i>
<b>50 000 грн</b>

<b><u>Страхові виплати</u></b>

<b>Виплата у випадку смерті дисконтованої страхової суми (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
|  2              |  118 209,26 |
|  6              |  191 208,17 |
| 11              |  308 097,96 |
| 16              |  497 361,03 |
| 20              |  743 129,59 |
</code></pre>

<b>Виплата у разі смерті внаслідок нещасного випадку (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
|  2              |  168 209,26 |
|  6              |  241 208,17 |
| 11              |  358 097,96 |
| 16              |  547 361,03 |
| 20              |  793 129,59 |
</code></pre>

<b>Виплата по закінченні строку дії договору (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
| 20              |  465 109,69 |
</code></pre>


Звідси Ви можете перейти до індивідуального розрахунку страхової виплати
Програми ГРАВЕ Універсал  ⬇️⬇️⬇️";
                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [CALCULATION_PROG . " " . UNIVERSAL],
                        [OTHER_PERS_PROGRAMS],
                        [CONSULTATION],
                    ];
                    // Get the keyboard
                    $keyb = $telegram->buildKeyBoard($option, true, true, false);
                    $content = [
                        'chat_id' => $chat_id,
                        'reply_markup' => $keyb,
                        'parse_mode' => "html",
                        'protect_content' => 'true',
                        'text' => $reply,
                    ];
                    $telegram->sendMessage($content);
                }
                break;

            case OTHER_PERS_PROGRAMS:
            case RETURN_LIST_HELTH_PROT:
                if (!$telegram->messageFromGroup()) {
                    $reply = "Програми <b>Захисту здоров’я</b>";
                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [GRAVE_MEDIC],
                        [SUPPORT_HEALTH_1],
                        [SUPPORT_HEALTH_3],
                        [CONSULTATION],
                    ];
                    // Get the keyboard
                    $keyb = $telegram->buildKeyBoard($option, true, true, false);
                    $content = [
                        'chat_id' => $chat_id,
                        'reply_markup' => $keyb,
                        'parse_mode' => "html",
                        'protect_content' => 'true',
                        'text' => $reply,
                    ];
                    $telegram->sendMessage($content);
                }
                break;

            case GRAVE_MEDIC:
                if (!$telegram->messageFromGroup()) {
                    $reply = "ЗАХИСТ ЗА ДВОМА ПРОГРАМАМИ СТРАХУВАННЯ<b>ГРАВЕ Медик</b>\n
Багато хто з нас неодноразово замислювався над тим: що є справжнім скарбом у житті? 
Дехто скаже - скриня із золотом, а інший - щастя. Думок стільки ж, скільки і людей. Але головним багатством ми можемо назвати саме - здоров’я. 

Той, хто має міцне здоров’я, скептично поставиться до цього, адже він не обділений цим скарбом і його можна по праву назвати багатою людиною. Як тільки людина починає хворіти або отримує травму, вона відразу ж міняє свою думку. Адже хвороба повністю може змінити життя. Заможна і могутня людина раптом стає безпорадною і витрачає величезні кошти та час для того щоб повернутися до повноцінного життя. 

В такій складній ситуації додаткова підтримка є дуже важливою, оскільки людина не може працювати і повинна концентруватися на відновлені свого здоров’я. З цією метою «ГРАВЕ УКРАЇНА Страхування життя» пропонує програму «ГРАВЕ Медик», яка передбачає одночасний захист за двома програмами страхування: накопичення коштів та страхування настання ризиків. «ГРАВЕ Медик» надасть суттєву фінансову підтримку в разі хірургічного втручання і стаціонарного лікування в медичному закладі та допоможе накопичити значну суму капіталу, яку клієнт отримає по закінченні строку страхування.
";
                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [GRAVE_MEDIC_ACCUMULATION],
                        [GRAVE_MEDIC_RISK_INS],
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
                }
                break;

            case SUPPORT_HEALTH_1:
                if (!$telegram->messageFromGroup()) {
                    $reply = "ЗАХИСТИ СВОЄ ЗДОРОВ’Я ТА ЗДОРОВ’Я СВОЄЇ ДИТИН <b>ГРАВЕ Підтримка</b>\n
Захист здоров’я дорослого і всіх його дітей на випадок діагностування важкої хвороби з можливістю накопичення суттєвого капіталу, так необхідного для реалізації намічених цілей, та з одночасним захистом дорослого на випадок настання тривалої інвалідності у розмірі 30% і вище внаслідок нещасного випадку.

<b><u>ГРАВЕ Підтримка гарантує</u></b>

Накопичення коштів
Виплату повної гарантованої страхової суми разом із накопиченими резервами бонусів після закінчення дії договору.

Захист життя
Виплату вигодонабувачам повної гарантованої страхової суми разом із накопиченими резервами бонусів, у випадку, якщо застрахована особа йде з життя протягом дії договору страхування.

Захист здоров’я
• Виплату застрахованій особі повної гарантованої страхової суми разом із накопиченими резервами бонусів у випадку діагностування однієї з 20 важких хвороб. Страхова виплата здійснюється виключно тільки за одним страховим випадком, навіть якщо протягом строку дії договору страхування було діагностовано декілька певних важких хвороб. За фактом здійснення страхової виплати договір припиняє свою дію.
• Виплату застрахованій особі 25% повної гарантованої страхової суми у випадку діагностування у біологічної або усиновленої дитини однієї з 12 важких хвороб. Під страховим захистом автоматично знаходяться всі без виключення діти застрахованої особи від 3 до 16 років. Страхові виплати здійснюються максимум по 4 дітям. Договір продовжує свою дію.

Захист у випадку інвалідності
Виплату певного відсотку страхової суми, розмір якого відповідає ступеню встановленої інвалідності у разі, якщо протягом дії договору застрахована особа внаслідок нещасного випадку отримує інвалідність 30% і вище. Разом із цим договір продовжує свою дію.

<b><u>Приклад ГРАВЕ Підтримка</u></b>

<i>Застрахована особа:</i>
<b>Чоловік віком 30 рокі</b>
<i>Строк страхування:</i>
<b>20 років</b>
<i>Щорічний страховий платіж:</i>
<b>10 000 грн</b>
<i>Гарантована страхова сума:</i>
<b>183 833 грн</b>

<b><u>Страхові виплати</u></b>

<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
|  2              |   183 833,00 |
|  6              |  188 317,20 |
| 11              |  211 787,74 |
| 16              |  270 164,05 |
</code></pre>

<b>Виплата по закінченні строку дії договору (разом із бонусами), грн</b>
<pre><code class='language-python'>
| Рік страхового  | Сума        |
| випадку         |             |
|-----------------|-------------|
| 20              |  371 573,47 |
</code></pre>


Звідси Ви можете перейти до індивідуального розрахунку страхової виплати
Програми ГРАВЕ Підтримка  ⬇️⬇️⬇️";

                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [CALCULATION_PROG . " " . SUPPORT_HEALTH_1],
                        [RETURN_LIST_HELTH_PROT],
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
                }
                break;

            case SUPPORT_HEALTH_3:
                if (!$telegram->messageFromGroup()) {
                    $reply = "ЗАХИСТ ЗА ДВОМА ПРОГРАМАМИ СТРАХУВАННЯ <b>ГРАВЕ Підтримка 3</b>\n
Тривалість життя людини з кожним роком зростає. Це стало можливим завдяки розвитку медицини, росту якості життя, своєчасному діагностуванню та лікуванню. Але за даними ВОЗ половина населення планети не має медичного страхування. Понад 800 мільйонів людей − майже 12 відсотків населення світу − витрачають, як мінімум, 10 відсотків свого сімейного бюджету на послуги в сфері охорони здоров’я. Мільйони сімей у світі потрапляють у боргову яму через витрати на охорону здоров’я. Нажаль під цю невтішну статистику попадають і громадяни України.

Тому з метою забезпечення загального доступу до якісного медичного обслуговування ми розробили збалансовану програму з захисту здоров’я від трьох найбільш поширених в Україні критичних захворювань, які часто стають причиною тривалої непрацездатності людини або навіть смерті. Програма «ГРАВЕ ПІДТРИМКА 3» стане справжньою фінансовою підтримкою у випадку діагностування одного з трьох важких захворювань (інсульту, інфаркту або онкології). Крім того, «ГРАВЕ ПІДТРИМКА 3» передбачає одночасний захист за двома програмами страхування: накопичення коштів та страхування на випадок діагностування важкої хвороби вперше.
";
                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [ACCUMULATION],
                        [SUPPORT_3_HARD],
                        [RETURN_LIST_HELTH_PROT],
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
                }
                break;

            case GRAVE_MEDIC_ACCUMULATION:
                if (!$telegram->messageFromGroup()) {
                    $reply = "НАКОПИЧУВАЛЬНА ПРОГРАМА <b>ГРАВЕ Медик</b>\n
Під час укладання договору страхування за програмою «ГРАВЕ Медик» клієнт на власний розсуд обирає один із трьох, запропонованих «ГРАВЕ УКРАЇНА Страхування життя», варіантів накопичувальної програми страхування.

<i><b>Варіант 1 (тариф GX-1)</b></i> передбачає негайну виплату повної гарантованої страхової суми разом із накопичувальними бонусами у випадку смерті застрахованої особи.

<i><b>Варіант 2 (тариф GX-3)</b></i> передбачає виплату повної гарантованої страхової суми із накопиченими бонусами по закінченні строку страхування, як у випадку дожиття, так і смерті застрахованої особи з можливістю отримання дисконтованої страхової суми протягом дії договору.

<i><b>Варіант 3 (тариф GX-5S)</b></i> передбачає негайну виплату диференційованої страхової суми разом із накопиченими бонусами у випадку смерті застрахованої особи.

Накопичення, збереження, примноження коштів
Виплату повної гарантованої страхової суми разом із накопиченими резервами бонусів після закінчення дії договору страхування. 

Захист життя
<i>За варіантом 1 (тариф GX-1)</i> вигодонабувачам негайно виплачується повна гарантована страхова сума разом із накопиченими резервами бонусів, у випадку, якщо застрахована особа йде з життя. Договір при цьому припиняє свою дію.

<i>За варіантом 2 (тариф GX-3)</i> вигодонабувачам виплачується повна гарантована страхова сума разом із накопиченими резервами бонусів після закінчення дії договору, у випадку, якщо застрахована особа йде з життя. При цьому протягом дії договору існує можливість отримати дисконтовану гарантовану страхову суму.

<i>За варіантом 3 (тариф GX-5S)</i> вигодонабувачам негайно виплачується частина гарантованої страхової суми пропорційної часу дії договору страхування разом із накопиченими резервами бонусів, у випадку, якщо застрахована особа йде з життя. Договір при цьому припиняє свою дію.


Звідси Ви можете перейти до індивідуального розрахунку страхової виплати
Програми " . GRAVE_MEDIC_ACCUMULATION . "⬇️⬇️⬇️";

                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [CALCULATION_PROG . " " . GRAVE_MEDIC_ACCUMULATION],
                        [RETURN_LIST_HELTH_PROT],
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
                }
                break;
            case ACCUMULATION:
                if (!$telegram->messageFromGroup()) {
                    $reply = "НАКОПИЧУВАЛЬНА ПРОГРАМА <b>ГРАВЕ Підтримка 3</b>\n

Під час укладання договору страхування за програмою «ГРАВЕ Підтримка 3» клієнт на власний розсуд обирає один із трьох, запропонованих «ГРАВЕ УКРАЇНА Страхування життя», варіантів накопичувальної програми страхування.

<i><b>Варіант 1 (тариф GX-1)</b></i> передбачає негайну виплату повної гарантованої страхової суми разом із накопичувальними бонусами у випадку смерті застрахованої особи..

<i><b>Варіант 2 (тариф GX-3)</b></i> передбачає виплату повної гарантованої страхової суми із накопиченими бонусами по закінченні строку страхування, як у випадку дожиття, так і смерті застрахованої особи з можливістю отримання дисконтованої страхової суми протягом дії договору.

<i><b>Варіант 3 (тариф GX-5S)</b></i> передбачає негайну виплату диференційованої страхової суми разом із накопиченими бонусами у випадку смерті застрахованої особи.

Накопичувальна програма гарантує
Накопичення, збереження, примноження коштів

Виплату повної гарантованої страхової суми разом із накопиченими резервами бонусів після закінчення дії договору страхування.

 

Захист життя

<i>За варіантом 1 (тариф GX-1)</i> вигодонабувачам негайно виплачується повна гарантована страхова сума разом із накопиченими резервами бонусів, у випадку, якщо застрахована особа йде з життя. Договір при цьому припиняє свою дію.

<i>За варіантом 2 (тариф GX-3)</i> вигодонабувачам виплачується повна гарантована страхова сума разом із накопиченими резервами бонусів після закінчення дії договору, у випадку, якщо застрахована особа йде з життя. При цьому протягом дії договору існує можливість отримати дисконтовану гарантовану страхову суму.

<i>За варіантом 3 (тариф GX-5S)</i> вигодонабувачам негайно виплачується частина гарантованої страхової суми пропорційної часу дії договору страхування разом із накопиченими резервами бонусів, у випадку, якщо застрахована особа йде з життя. Договір при цьому припиняє свою дію.


Звідси Ви можете перейти до індивідуального розрахунку страхової виплати
Програми ГРАВЕ Підтримка 3  ⬇️⬇️⬇️";

                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [CALCULATION_PROG . " " . ACCUMULATION],
                        [RETURN_LIST_HELTH_PROT],
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
                }
                break;
            case SUPPORT_3_HARD:
                if (!$telegram->messageFromGroup()) {
                    $reply = "СТРАХУВАННЯ ВАЖКОЇ ХВОРОБИ <b>ГРАВЕ Підтримка 3</b>\n

Програма передбачає захист здоров’я на випадок діагностування вперше однієї з 3-х важких хвороб: інсульту, інфаркту та злоякісного новоутворення, разом із додатковим захистом життя. Страхування на випадок діагностування однієї з 3-х важких хвороб можна придбати лише разом з одним із трьох варіантів накопичувальної програми.
Страхування на випадок важкої хвороби гарантує

 

Додатковий захист життя

Негайну виплату вигодонабувачам (додатково до виплати із захисту життя за обраним варіантом накопичувальної програми) 2 500 грн або 500 USD, у випадку, якщо застрахована особа йде з життя.

 

Захист здоров’я
Виплату застрахованій особі повної гарантованої страхової суми за випадком діагностування вперше однієї з 3-х важких хвороб: інсульту, інфаркту або злоякісного новоутворення. 

Страхова виплата здійснюється виключно тільки за одним страховим випадком, навіть якщо протягом строку дії договору страхування було діагностовано декілька певних важких хвороб. За фактом здійснення страхової виплати договір припиняє свою дію.

Розміри можливих страхових сум за випадком діагностування однієї з 3-х важких хвороб наведені в таблиці.


Звідси Ви можете перейти до індивідуального розрахунку страхової виплати
Програми " . SUPPORT_3_HARD . "⬇️⬇️⬇️";

                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [CALCULATION_PROG . " " . SUPPORT_3_HARD],
                        [RETURN_LIST_HELTH_PROT],
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
                }
                break;

            case GRAVE_MEDIC_RISK_INS:
                if (!$telegram->messageFromGroup()) {
                    $reply = "СТРАХУВАННЯ НАСТАННЯ РИЗИКІВ <b>ГРАВЕ Медик</b>\n
Програма передбачає захист здоров’я за двома додатковими тарифами (KОXZ та KHXZ) на випадок хірургічної операції та стаціонарного лікування в медичному закладі разом із додатковим захистом життя за тарифом
RX-1. Страхування настання ризиків можна придбати лише разом з одним із трьох варіантів накопичувальної програми.
<u><b>Страхування настання ризиків гарантує</b></u> 

Додатковий захист життя
Негайну виплату вигодонабувачам (додатково до виплати із захисту життя за обраним варіантом накопичувальної програми) 2 500 грн або 500 USD, у випадку, якщо застрахована особа йде з життя.

Захист у випадку госпіталізації
• Виплату застрахованій особі страхової суми щодоби за умови перебування на стаціонарному лікуванні в медичній установі не менше ніж 5 днів. Протягом року страхове покриття розповсюджується на 180 діб перебування у лікарні.
• Виплату застрахованій особі страхової суми щодоби за 5 днів, незалежно від часу фактичного перебування в медичному закладі з приводу пологів за наявності свідоцтва про народження дитини.

Захист у випадку хірургічної операції
Виплату застрахованій особі встановленого відсотку гарантованої страхової суми за хірургічним втручанням. Види операцій та відсотки виплат за ними більш детально описані в Правилах ПрАТ «ГРАВЕ УКРАЇНА Страхування життя».

Звідси Ви можете перейти до індивідуального розрахунку страхової виплати
Програми " . GRAVE_MEDIC_RISK_INS ." ⬇️⬇️⬇️";

                    // Create option for the custom keyboard. Array of array string
                    $option = [
                        [CALCULATION_PROG . " " . GRAVE_MEDIC_RISK_INS],
                        [RETURN_LIST_HELTH_PROT],
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
                }
                break;
            case CALCULATION_PROG . " " . SEP:
                if (!$telegram->messageFromGroup()) {
                    $reply = "Для розрахунку <b>" .SEP . "</b> мені знадобляться деякі дані про Вас.
<i>Продовжимо</i>?";

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

                    // Continue Yes.
                    $reply = '👇';
                    $content = [
                        'chat_id' => $chat_id,
                        'reply_markup' => json_encode([
                            'inline_keyboard' => [
                                [
                                    [
                                        'text' => "Так",
                                        'callback_data' => 'continue_sep',
                                    ],

                                ],
                                [
                                    [
                                    'text' => "Повернутись до головного меню",
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

                }
                break;

            default:
                break;
        }

        if ($text === '/ask' && !$telegram->messageFromGroup()) {
            $content = [
                'chat_id' => $chatId,
                'text' => "Ви можете безпосередньо поставити запитання щодо страхових програм та отримати безкоштовну консультацію <b>експерта</b>.",
                'parse_mode' => "html",
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            [
                                'text' => CALL_LORA,
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

        /* ================================================== */

    }
}
/* ================================================== */
