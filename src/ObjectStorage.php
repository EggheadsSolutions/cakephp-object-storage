<?php
declare(strict_types=1);

namespace Eggheads\CakephpObjectStorage;

use Cake\Core\Configure;
use Eggheads\CakephpObjectStorage\Exception\ObjectStorageException;
use Psr\Http\Message\StreamInterface;

/**
 * Класс для работы с Object Storage
 * Прокси над клиентами YandexClient, FileClient
 */
final class ObjectStorage implements ObjectStorageInterface
{
    /**
     * Клиент
     *
     * @var ObjectStorageInterface
     */
    private ObjectStorageInterface $_client;

    /**
     * @param ObjectStorageInterface $client
     */
    public function __construct(ObjectStorageInterface $client)
    {
        $this->_client = $client;
    }

    /**
     * Получение объекта хранилища, в зависимости от окружения
     *
     * @return ObjectStorageInterface
     * @throws ObjectStorageException
     */
    public static function getInstance(): ObjectStorageInterface
    {
        $clientName = ObjectStorageConfig::getClientName();
        if (!is_null($clientName)) {
            $clientName = __NAMESPACE__ . '\\' . Configure::read('ObjectStorageClient');

            // Проверяем, что класс существует и имплементирует ObjectStorageInterface
            if (!class_exists($clientName) || !in_array(ObjectStorageInterface::class, class_implements($clientName), true)) {
                throw new ObjectStorageException('Неверный клиент для ObjectStorage');
            }
            return new self($clientName::getInstance());
        }
        return new self(YandexClient::getInstance());
    }

    /** @inheritdoc */
    public function putObject(string $bucketName, string $key, $object = null, ?string $filePath = null): ?string
    {
        return $this->_client->putObject($bucketName, $key, $object, $filePath);
    }

    /** @inheritdoc */
    public function deleteObject(string $bucketName, string $key): bool
    {
        return $this->_client->deleteObject($bucketName, $key);
    }

    /** @inheritdoc */
    public function getObject(string $bucketName, string $key): ?StreamInterface
    {
        return $this->_client->getObject($bucketName, $key);
    }

    /** @inheritdoc */
    public function download(string $bucketName, string $key): ?string
    {
        return $this->_client->download($bucketName, $key);
    }
}
