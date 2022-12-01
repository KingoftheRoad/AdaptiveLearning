<?php
$alpChatUrl = '';
switch(env('ALP_SERVER')){
    case "localhost":
            $alpChatUrl = env('ALP_CHAT_BASE_URL', 'http://localhost/alp_chat/');
            break;
    case "alpweb":
            $alpChatUrl = env('ALP_CHAT_BASE_URL', 'http://localhost/alp_web_chat/');
            break;
    case "alp3":
            $alpChatUrl = env('ALP_CHAT_BASE_URL', 'http://localhost/alp_chat/');
            break;
    default:
        $alpChatUrl = env('ALP_CHAT_BASE_URL', 'https://chat.scal-p.org/alp_chat/');
        break;
}

return [
    'alp_chat_url' => $alpChatUrl,
    'alp_chat_group_url' => $alpChatUrl."group",
];