<?php
declare(strict_types=1);

namespace Eggheads\CakephpObjectStorage;

use Eggheads\CakephpObjectStorage\Traits\Library;

final class ObjectStorageHelper
{
    use Library;

    /**
     * Формирование ключей для бакетов
     *
     * Для хранения файлов в бакете предполагается следующая древовидная структурв
     * ИД сущности (это может быть идентификатор кабинета или пользователя)
     * -- Префикс операции (файлы какого типа мы храним ниже, например cost дял себесов, predict - для расчётов поставок)
     * ---- Имя файла + случайная примесь (к оригинальному имени файла добавляется уникальная примесь)
     *
     * ИД операции и Префикс можно опускать, если это не требуется для задачи
     * В простейшем варианте, ключ в бакете = имени загруженного файла
     *
     * Варианты вызовов и получающиеся ключи также можно посмотреть в тесте
     *
     * @param string $fileName Имя файла
     * @param string|null $prefix Префикс операции
     * @param int|null $id Идентификатор сущности
     * @param bool $isSkipUniqueLabel Пропускать ли добавление уникальной примеси
     * @return string
     * @see ObjectStorageHelperTest::testGetName()
     *
     */
    public static function getKey(string $fileName, ?string $prefix = null, ?int $id = null, bool $isSkipUniqueLabel = false): string
    {
        $keyArr = [];
        if (!is_null($id)) {
            $keyArr[] = $id;
        }
        if (!is_null($prefix)) {
            $keyArr[] = $prefix;
        }
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileExtWithPoint = !empty($fileExt) ? '.' . $fileExt : '';
        $fileBaseName = basename($fileName, $fileExtWithPoint);
        $fileBaseName = (string)preg_replace('/[\/\\\,\"\']/', '_', $fileBaseName);
        $uniqueLabel = !$isSkipUniqueLabel ? self::_uniqueLabel() : '';
        $keyArr[] = $fileBaseName . $uniqueLabel . $fileExtWithPoint;
        return implode(DS, $keyArr);
    }

    /**
     * Уникальная примесь для ключа
     *
     * @return string
     */
    private static function _uniqueLabel(): string
    {
        return (string)time();
    }
}
