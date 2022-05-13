<?php
declare(strict_types=1);

namespace Eggheads\CakephpObjectStorage\Tests;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Eggheads\CakephpObjectStorage\Exception\ObjectStorageException;
use Eggheads\CakephpObjectStorage\ObjectStorageConfig;

class ObjectStorageConfigTest extends TestCase
{
    /**
     * Для сохранения конфига
     *
     * @var array<mixed>|null
     */
    private ?array $_savedConfig;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $this->_savedConfig = Configure::read(ObjectStorageConfig::CONFIG_YANDEX_STORAGE);
        parent::setUp();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        Configure::write(ObjectStorageConfig::CONFIG_YANDEX_STORAGE, $this->_savedConfig);
        parent::tearDown();
    }

    /**
     * Тестируем getYandexStorageCredentials (не настроен конфиг)
     *
     * @return void
     * @throws ObjectStorageException
     * @see ObjectStorageConfig::getYandexStorageCredentials()
     */
    public function testGetYandexStorageCredentialsNoConfig(): void
    {
        $this->expectExceptionMessage('Не настроены доступы к YandexStorage');
        Configure::write(ObjectStorageConfig::CONFIG_YANDEX_STORAGE, null);
        ObjectStorageConfig::getYandexStorageCredentials();
    }

    /**
     * Тестируем getYandexStorageCredentials (конфиг настроен неправильно)
     *
     * @return void
     * @throws ObjectStorageException
     * @see ObjectStorageConfig::getYandexStorageCredentials()
     */
    public function testGetYandexStorageCredentialsBadConfig(): void
    {
        $this->expectExceptionMessage('Неправильно настроены доступы к YandexStorage');
        $wrongConfig = ObjectStorageConfig::EMPTY_YANDEX_STORAGE_CONFIG;
        unset($wrongConfig['endpoint']);
        Configure::write(ObjectStorageConfig::CONFIG_YANDEX_STORAGE, $wrongConfig);
        ObjectStorageConfig::getYandexStorageCredentials();
    }

    /**
     * Тестируем getYandexStorageCredentials (конфиг настроен)
     *
     * @return void
     * @throws ObjectStorageException
     * @see ObjectStorageConfig::getYandexStorageCredentials()
     */
    public function testGetYandexStorageCredentials(): void
    {
        Configure::write(ObjectStorageConfig::CONFIG_YANDEX_STORAGE, ObjectStorageConfig::EMPTY_YANDEX_STORAGE_CONFIG);
        self::assertEquals(ObjectStorageConfig::EMPTY_YANDEX_STORAGE_CONFIG, ObjectStorageConfig::getYandexStorageCredentials());
    }
}
