<?php
declare(strict_types=1);

namespace Eggheads\CakephpObjectStorage\Tests;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Eggheads\CakephpObjectStorage\Exception\ObjectStorageException;
use Eggheads\CakephpObjectStorage\StorageConfig;

class StorageConfigTest extends TestCase
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
        $this->_savedConfig = Configure::read(StorageConfig::CONFIG_YANDEX_STORAGE);
        parent::setUp();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        Configure::write(StorageConfig::CONFIG_YANDEX_STORAGE, $this->_savedConfig);
        parent::tearDown();
    }

    /**
     * Тестируем getYandexStorageCredentials (не настроен конфиг)
     *
     * @return void
     * @throws ObjectStorageException
     * @see StorageConfig::getYandexStorageCredentials()
     */
    public function testGetYandexStorageCredentialsNoConfig(): void
    {
        $this->expectExceptionMessage('Не настроены доступы к YandexStorage');
        Configure::write(StorageConfig::CONFIG_YANDEX_STORAGE, null);
        StorageConfig::getYandexStorageCredentials();
    }

    /**
     * Тестируем getYandexStorageCredentials (конфиг настроен неправильно)
     *
     * @return void
     * @throws ObjectStorageException
     * @see StorageConfig::getYandexStorageCredentials()
     */
    public function testGetYandexStorageCredentialsBadConfig(): void
    {
        $this->expectExceptionMessage('Неправильно настроены доступы к YandexStorage');
        $wrongConfig = StorageConfig::EMPTY_YANDEX_STORAGE_CONFIG;
        unset($wrongConfig['endpoint']);
        Configure::write(StorageConfig::CONFIG_YANDEX_STORAGE, $wrongConfig);
        StorageConfig::getYandexStorageCredentials();
    }

    /**
     * Тестируем getYandexStorageCredentials (конфиг настроен)
     *
     * @return void
     * @throws ObjectStorageException
     * @see StorageConfig::getYandexStorageCredentials()
     */
    public function testGetYandexStorageCredentials(): void
    {
        Configure::write(StorageConfig::CONFIG_YANDEX_STORAGE, StorageConfig::EMPTY_YANDEX_STORAGE_CONFIG);
        self::assertEquals(StorageConfig::EMPTY_YANDEX_STORAGE_CONFIG, StorageConfig::getYandexStorageCredentials());
    }

    /**
     * Ntcn
     *
     * @return void
     */
    public function testTe(): void
    {
        self::assertTrue(false);
    }
}
