<?php
declare(strict_types=1);

return [
'yandexStorage' => [
'version' => 'latest',
'endpoint' => 'https://storage.yandexcloud.net',
'region' => 'ru-central1',
'credentials' => [
'key' => env('YANDEX_STORAGE_KEY'), // Идентификатор клиента
'secret' => env('YANDEX_STORAGE_SECRET'), // Секретный ключ
],
],
];