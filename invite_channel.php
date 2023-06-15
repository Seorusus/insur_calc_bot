<?php
include_once 'settings.php';

// Instances the class
$telegram = new Telegram(TG_TOKEN);

// Get the update data
$result = $telegram->getData();

$update = json_decode(file_get_contents('php://input'), true);
$channel = CHANNEL_ID;
$message = $result['message'];
$forward_from_chat = $message['forward_from_chat'];
$channel = $forward_from_chat['id'];
if (isset($update['message']) && $update['message']['chat']['id'] == $channel) {
    $text = 'Привет, мир!';
    $content = ['chat_id' => $channel, 'text' => $text];
    $telegram->sendMessage($content);
}

// Check if the update has a message
if (isset($result['message'])) {
    $message = $result['message'];

    // Check if the message is forwarded from a channel
    if (isset($message['forward_from_chat'])) {
        $forward_from_chat = $message['forward_from_chat'];
        $channel_id = $forward_from_chat['id'];
        $textMessage = 'Ви можете скористатися Страховим калькулятором, щоб розрахувати вартість страхових внесків та виплат,
а також ознайомитись з різними видами страховки.

Або отримати консультацію експерта ⬇️⬇️⬇️';
        // Check if the message text is NOT the text we send
        if ($message['text'] !== $textMessage) {
            $gif = curl_file_create('images/waiting-1-min.gif', 'image/gif');
            $content = [
                'chat_id' => $channel_id,
                'animation' => $gif
            ];

            $response = $telegram->sendAnimation($content);
            // Set the inline keyboard
            $inline_keyboard = [
                [
                    [
                        'text' => INS_CALCULATOR,
                        'url' => 'https://t.me/insurance_calc_bot',
                    ]
                ],
                [
                    [
                        'text' => CALL_LORA,
                        'url' => 'https://t.me/larisa_lonchar',
                    ]
                ]
            ];


            // Send a message with the inline keyboard to the channel
            $telegram->sendMessage([
                'chat_id' => $channel_id,
                'text' => $textMessage,
                'reply_markup' => json_encode(['inline_keyboard' => $inline_keyboard])
            ]);

            die();
        }
    }
}
