<?php
declare(strict_types=1);

namespace Eggheads\CakephpObjectStorage\Tests;

use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\TestSuite\TestCase;
use Eggheads\CakephpObjectStorage\Exception\ObjectStorageException;
use Eggheads\CakephpObjectStorage\FileClient;
use Eggheads\CakephpObjectStorage\ObjectStorage;
use Eggheads\CakephpObjectStorage\ObjectStorageConfig;
use Eggheads\CakephpObjectStorage\YandexClient;
use Eggheads\Mocks\MethodMocker;
use Exception;

class ObjectStorageTest extends TestCase
{
    /** @var string Бакет для интеграционных тестов */
    private const TEST_BUCKET = 'dev-eh-integration-test';

    /**
     * Данные для теста
     *
     * @return iterable<array<string>>
     */
    public function dataProviderTestStorage(): iterable
    {
        yield 'Файловый клиент' => ['FileClient'];
        yield 'Клиент Yandex Storage' => ['YandexClient'];
    }

    /**
     * Тестируем полный цикл записи, чтения, удаления из бакета
     *
     * @param string $clientName
     * @return void
     * @throws ObjectStorageException
     * @throws Exception
     * @group integration
     * @dataProvider dataProviderTestStorage
     */
    public function testWriteFromString(string $clientName): void
    {
        $testKey = 'testKey1';
        $testString = 'fake1';
        $testBucket = self::TEST_BUCKET;

        Configure::write(ObjectStorageConfig::CONFIG_CLIENT, $clientName);

        $client = ObjectStorage::getInstance();

        /** @var string Ожидаемый url загруженного объекта */
        $expectObjectUrl = $clientName === 'FileClient' ? FileClient::FAKE_URL :
            sprintf('https://%s.%s/%s', $testBucket, YandexClient::YANDEX_STORAGE_URL, $testKey);

        self::assertEquals(
            $expectObjectUrl,
            $client->putObject($testBucket, $testKey, $testString)
        );

        self::assertEquals(
            $testString,
            $client->getObject($testBucket, $testKey)->read(strlen($testString))
        );

        self::assertTrue(
            $client->deleteObject($testBucket, $testKey)
        );

        if ($clientName === 'FileClient') {
            MethodMocker::mock(Log::class, 'error')
                ->singleCall()
                ->willReturnValue(true);
        }

        self::assertNull($client->getObject($testBucket, $testKey));
    }

    /**
     * Тестируем запись в бакет из файла
     *
     * @param string $clientName
     * @return void
     * @group integration
     * @dataProvider dataProviderTestStorage
     * @throws ObjectStorageException
     */
    public function testWriteFromFile(string $clientName): void
    {
        $testKey = 'fileTestKey2';
        $testString = 'fake2';
        $testBucket = self::TEST_BUCKET;

        Configure::write(ObjectStorageConfig::CONFIG_CLIENT, $clientName);

        $client = ObjectStorage::getInstance();

        // Создаем файл на диске
        $filePath = TMP . 'YandexObjectStorageTestWriteFromFile.txt';
        file_put_contents($filePath, $testString);

        /** @var string Ожидаемый url загруженного объекта */
        $expectObjectUrl = $clientName === 'YandexClient' ?
            sprintf('https://%s.%s/%s', $testBucket, YandexClient::YANDEX_STORAGE_URL, $testKey) :
            FileClient::FAKE_URL;

        self::assertEquals(
            $expectObjectUrl,
            $client->putObject($testBucket, $testKey, null, $filePath)
        );

        self::assertEquals(
            $testString,
            $client->getObject($testBucket, $testKey)->read(strlen($testString))
        );

        self::assertTrue(
            $client->deleteObject($testBucket, $testKey)
        );
    }

    /**
     * Тестируем скачивание
     *
     * @group integration
     * @param string $clientName
     * @return void
     * @dataProvider dataProviderTestStorage
     * @throws ObjectStorageException
     */
    public function testDownload(string $clientName): void
    {
        $testKey = 'fileTestKey3';
        $testString = 'fake3';
        $testBucket = self::TEST_BUCKET;

        Configure::write(ObjectStorageConfig::CONFIG_CLIENT, $clientName);

        $client = ObjectStorage::getInstance();

        $client->putObject($testBucket, $testKey, $testString);

        $fileName = $client->download($testBucket, $testKey);

        $downloadTestString = file_get_contents($fileName);
        self::assertEquals($downloadTestString, $testString);
    }
}
