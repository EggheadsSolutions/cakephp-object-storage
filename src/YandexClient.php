<?php
declare(strict_types=1);

namespace Eggheads\CakephpObjectStorage;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Cake\Core\Configure;
use Cake\Log\Log;
use Eggheads\CakephpObjectStorage\Exception\ObjectStorageException;
use Eggheads\CakephpObjectStorage\Traits\Singleton;
use Psr\Http\Message\StreamInterface;

class YandexClient implements ObjectStorageInterface
{
    use Singleton;

    /** @var string URL Yandex storage */
    public const YANDEX_STORAGE_URL = 'storage.yandexcloud.net';

    /** @var int Максимальное количество объектов в одном запросе */
    private const DEFAULT_OBJECT_COUNT = 1000;

    /** @var string Папка для хранения локальных файлов */
    private const STORAGE_DIRECTORY = TMP . 'local_storage' . DS;

    /** @var S3Client */
    private S3Client $_s3Client;

    /**
     * @inheritdoc
     * @throws ObjectStorageException
     */
    private function __construct()
    {
        $this->_s3Client = new S3Client(StorageConfig::getYandexStorageCredentials());
    }

    /**
     * @inheritdoc
     * @throws ObjectStorageException
     */
    public function putObject(string $bucketName, string $key, $object = null, ?string $filePath = null): ?string
    {
        if (is_null($object) && is_null($filePath)) {
            throw new ObjectStorageException('Необходимо, чтобы один из параметров $object или $filePath был задан');
        }
        $params = [
            'Bucket' => $bucketName,
            'Key' => $key,
        ];
        if (!is_null($object)) {
            $params['Body'] = $object;
        } else {
            $params['SourceFile'] = $filePath;
        }
        try {
            return $this->_s3Client
                ->putObject($params)
                ->get('ObjectURL');
        } catch (S3Exception $s3Exception) {
            Log::error($s3Exception->getMessage());
        }
        return null;
    }

    /** @inheritdoc */
    public function deleteObject(string $bucketName, string $key): bool
    {
        try {
            $this->_s3Client
                ->deleteObject([
                    'Bucket' => $bucketName,
                    'Key' => $key,
                ]);
            return true;
        } catch (S3Exception $s3Exception) {
            Log::error($s3Exception->getMessage());
        }
        return false;
    }

    /** @inheritdoc */
    public function getObject(string $bucketName, string $key): ?StreamInterface
    {
        try {
            return $this->_s3Client
                ->getObject([
                    'Bucket' => $bucketName,
                    'Key' => $key,
                ])->get('Body');
        } catch (S3Exception $s3Exception) {
            if ((int)$s3Exception->getStatusCode() !== 404) {
                Log::error($s3Exception->getMessage());
            }
        }
        return null;
    }

    /**
     * @inheritdoc
     * @throws ObjectStorageException
     */
    public function download(string $bucketName, string $key): ?string
    {
        // Создаем директорию, если не создана
        if (!is_dir(self::STORAGE_DIRECTORY)
            && !mkdir(self::STORAGE_DIRECTORY, 0755, true)
            && !is_dir(self::STORAGE_DIRECTORY)) {
            throw new ObjectStorageException(sprintf('Directory "%s" was not created', self::STORAGE_DIRECTORY));
        }
        $fileInfo = pathinfo($key);
        $fileName = tempnam(self::STORAGE_DIRECTORY, 'storage') . '.' . ($fileInfo['extension'] ?? '');
        $object = $this->getObject($bucketName, $key);
        return file_put_contents($fileName, $object) !== false ? $fileName : null;
    }

    /**
     * Удаление объектов из бакета
     * Для использования при разработке
     *
     * @param string $bucketName
     * @param int $limit Лимит (кратное 1000)
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethods)
     */
    private function _clearBucket(string $bucketName, int $limit = self::DEFAULT_OBJECT_COUNT): void
    {
        $maxCount = (int)ceil($limit / self::DEFAULT_OBJECT_COUNT);
        $count = 0;

        while ($count < $maxCount) {
            $count++;
            $list = $this->_s3Client->listObjects([
                'Bucket' => $bucketName,
            ]);
            $objects = $list->get('Contents');
            if (empty($objects)) {
                break;
            }
            $this->_s3Client->deleteObjects([
                'Bucket' => $bucketName,
                'Delete' => [
                    'Objects' => $objects,
                ],
            ]);
        }
    }
}
