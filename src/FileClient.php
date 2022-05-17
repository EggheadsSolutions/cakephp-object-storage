<?php
declare(strict_types=1);

namespace Eggheads\CakephpObjectStorage;

use Cake\Log\Log;
use Eggheads\CakephpObjectStorage\Exception\ObjectStorageException;
use Eggheads\CakephpObjectStorage\Lib\Dir;
use Eggheads\CakephpObjectStorage\Traits\Singleton;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamInterface;

/**
 * File Client
 *
 * @internal
 */
final class FileClient implements ObjectStorageInterface
{
    use Singleton;

    /** @var string Папка для хранения */
    public const STORAGE_DIRECTORY = TMP . 'object_storage' . DS;

    /** @var string Папка для хранения временных файлов */
    private const STORAGE_DIRECTORY_TMP = TMP . 'object_storage_tmp' . DS;

    /** @var bool Включение отключение логирования */
    private bool $_enableLog = true;

    /**
     * @inheritdoc
     * @throws ObjectStorageException
     */
    public function __construct()
    {
        // Создаем директории, если не созданы
        if (!Dir::createDir(self::STORAGE_DIRECTORY)) {
            throw new ObjectStorageException(sprintf('Directory "%s" was not created', self::STORAGE_DIRECTORY_TMP));
        }
        if (!Dir::createDir(self::STORAGE_DIRECTORY_TMP)) {
            throw new ObjectStorageException(sprintf('Directory "%s" was not created', self::STORAGE_DIRECTORY_TMP));
        }
    }

    /**
     * @inheritdoc
     * @throws ObjectStorageException
     */
    public function putObject(string $bucketName, string $key, $object = null, ?string $filePath = null): ?string
    {
        if ($this->_enableLog) {
            Log::info("Запись файла в бакет $bucketName с ключом $key");
        }
        if (is_null($object) && is_null($filePath)) {
            throw new ObjectStorageException('Необходимо, чтобы один из параметров $object или $filePath был задан');
        }
        $objectFilePath = $this->_getObjectFilePath($bucketName, $key);
        $result = is_null($filePath) ? file_put_contents($objectFilePath, $object) : copy($filePath, $objectFilePath);
        if ($result === false) {
            throw new ObjectStorageException('Проблемы с записью файла');
        }
        return $objectFilePath;
    }

    /** @inheritdoc */
    public function deleteObject(string $bucketName, string $key): bool
    {
        if ($this->_enableLog) {
            Log::info("Удаление файла из бакета $bucketName с ключом $key");
        }
        $objectFilePath = $this->_getObjectFilePath($bucketName, $key);
        return unlink($objectFilePath);
    }

    /**
     * @inheritdoc
     */
    public function getObject(string $bucketName, string $key): ?StreamInterface
    {
        if ($this->_enableLog) {
            Log::info("Получение файла из бакета $bucketName с ключом $key");
        }
        $objectFilePath = $this->_getObjectFilePath($bucketName, $key);
        if (is_file($objectFilePath)) {
            return new Stream(fopen($objectFilePath, 'r'));
        }
        Log::error('File not found');
        return null;
    }

    /** @inheritdoc */
    public function download(string $bucketName, string $key): ?string
    {
        if ($this->_enableLog) {
            Log::info("Скачивание файла из бакета $bucketName с ключом $key");
        }
        $objectFilePath = $this->_getObjectFilePath($bucketName, $key);
        $tmpFilePath = null;

        if (is_file($objectFilePath)) {
            $tmpFilePath = $this->_getObjectTmpFilePath($bucketName, $key);
            copy($objectFilePath, $tmpFilePath);
        }

        return is_file($tmpFilePath) ? $tmpFilePath : null;
    }

    /** Отключение логгирования */
    public function disableLog(): void
    {
        $this->_enableLog = false;
    }

    /** Включение логгирования */
    public function enableLog(): void
    {
        $this->_enableLog = true;
    }

    /**
     * Получаем путь к файлу
     *
     * @param string $bucketName
     * @param string $key
     * @return string
     */
    private function _getObjectFilePath(string $bucketName, string $key): string
    {
        return self::STORAGE_DIRECTORY . $this->_getObjectFileName($bucketName, $key);
    }

    /**
     * Получаем путь к временному файлу
     *
     * @param string $bucketName
     * @param string $key
     * @return string
     */
    private function _getObjectTmpFilePath(string $bucketName, string $key): string
    {
        return self::STORAGE_DIRECTORY_TMP . $this->_getObjectFileName($bucketName, $key);
    }

    /**
     * Получаем имя файла
     *
     * @param string $bucketName
     * @param string $key
     * @return string
     */
    private function _getObjectFileName(string $bucketName, string $key): string
    {
        return $bucketName . '_' . str_replace("/", '_', $key);
    }
}
