<?php
declare(strict_types=1);

namespace Eggheads\CakephpObjectStorage;

use Psr\Http\Message\StreamInterface;

interface ObjectStorageInterface
{
    /**
     * Добавление объекта в хранилище
     *
     * @param string $bucketName
     * @param string $key
     * @param string|StreamInterface|null $object
     * @param string|null $filePath
     * @return string|null
     */
    public function putObject(string $bucketName, string $key, $object = null, ?string $filePath = null): ?string;

    /**
     * Удаление объекта в хранилище
     *
     * @param string $bucketName Имя бакета
     * @param string $key Ключ в бакете
     * @return bool
     */
    public function deleteObject(string $bucketName, string $key): bool;

    /**
     * Получение объекта из хранилища
     *
     * @param string $bucketName Имя бакета
     * @param string $key Ключ в бакете
     * @return StreamInterface|null
     */
    public function getObject(string $bucketName, string $key): ?StreamInterface;

    /**
     * Сохранение файла из хранилища на локальный диск
     *
     * @param string $bucketName Имя бакета
     * @param string $key Ключ в бакете
     * @return string|null
     */
    public function download(string $bucketName, string $key): ?string;
}
