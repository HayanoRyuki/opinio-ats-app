<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Chrome拡張からのAPIリクエストを許可する設定。
    | Chrome拡張のオリジンは chrome-extension://<ID> 形式。
    |
    | ※ Chrome拡張の background.js / popup.js は host_permissions により
    |   CORS制約なしで通信可能だが、万が一のためにサーバー側も対応。
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'Accept', 'X-API-Key', 'Authorization'],

    'exposed_headers' => [],

    'max_age' => 86400, // 24時間

    'supports_credentials' => false,

];
