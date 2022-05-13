# Object storage for CakePHP

Библиотека для работы с хранилищем объектов. Позволяет хранить и получать доступ к объектам по ключу.

Имеет под капотом 2 клиента:
Файловый - хранение объектов в локальной файловой системе
Yandex - хранение объектов в YandexStorage

## Настройка
Добавить в файл конфигурации app_local.php настройки подключения к YandexStorage

```php
'yandexStorage' =>
    'version' => 'latest',
    'endpoint' => 'https://storage.yandexcloud.net',
    'region' => 'ru-central1',
    'credentials' => [
        'key' => '', // Идентификатор клиента
        'secret' => '', // Секретный ключ
        ],
    ],
```

Также
Для того чтобы по-умолчанию в тестах работал файловый клиент, необходимо в настройки тестов
ObjectStorageClient
