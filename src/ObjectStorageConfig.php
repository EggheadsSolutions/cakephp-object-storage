<?php
declare(strict_types=1);

namespace Eggheads\CakephpObjectStorage;

use Cake\Core\Configure;
use Eggheads\CakephpObjectStorage\Exception\ObjectStorageException;
use Eggheads\CakephpObjectStorage\Traits\Library;

/**
 * Работа с конфигами
 *
 * @internal
 */
final class ObjectStorageConfig
{
    use Library;

    /** @var string Поле настройки клиента в конфиге */
    public const CONFIG_CLIENT = 'objectStorageClient';

    /** @var string Поле настройки подключения к Yandex Storage в конфиге */
    public const CONFIG_YANDEX_STORAGE = 'yandexStorage';

    /** Пример Yandex Storage конфига */
    public const EMPTY_YANDEX_STORAGE_CONFIG = [
        'version' => '',
        'endpoint' => '',
        'region' => '',
        'credentials' => [
            'key' => '',
            'secret' => '',
        ],
    ];

    /**
     * Получить класс ObjectStorage из конфигурации
     *
     * @return string|null
     */
    public static function getClientName(): ?string
    {
        if (Configure::check(self::CONFIG_CLIENT) && is_string(Configure::read(self::CONFIG_CLIENT))) {
            return Configure::read(self::CONFIG_CLIENT);
        }
        return null;
    }

    /**
     * Получить настройки подключения к YandexStorage
     *
     * @return array{version:string, endpoint:string, region:string, credentials:array{key:string, secret:string}}
     * @throws ObjectStorageException
     */
    public static function getYandexStorageCredentials(): array
    {
        if (Configure::check(self::CONFIG_YANDEX_STORAGE)) {
            $config = Configure::read(self::CONFIG_YANDEX_STORAGE);
            if (!empty(array_diff_key(self::EMPTY_YANDEX_STORAGE_CONFIG, $config))) {
                throw new ObjectStorageException('Неправильно настроены доступы к YandexStorage');
            }
            return $config;
        }
        throw new ObjectStorageException('Не настроены доступы к YandexStorage');
    }
}
