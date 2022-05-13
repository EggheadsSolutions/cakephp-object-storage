<?php
declare(strict_types=1);

namespace Eggheads\CakephpObjectStorage;

use Eggheads\CakephpObjectStorage\Traits\Library;

/**
 * Управление логированием файлового клиента
 */
final class ObjectStorageMock
{
    use Library;

    /**
     * Выключение логирования ObjectStorage
     *
     * @see ObjectStorage
     * @see FileClient::disableLog()
     */
    public static function disableLog(): void
    {
        FileClient::getInstance()->disableLog();
    }

    /**
     * Включение логирования ObjectStorage
     *
     * @see ObjectStorage
     * @see FileClient::enableLog()
     */
    public static function enableLog(): void
    {
        FileClient::getInstance()->enableLog();
    }
}
