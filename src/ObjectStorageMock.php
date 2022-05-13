<?php
declare(strict_types=1);

namespace Eggheads\CakephpObjectStorage;

use Eggheads\CakephpObjectStorage\Traits\Library;

class ObjectStorageMock
{
    use Library;

    /**
     * Выключение логгирования ObjectStorage
     *
     * @see ObjectStorage
     * @see FileClient::disableLog()
     */
    public static function disableLog(): void
    {
        FileClient::getInstance()->disableLog();
    }

    /**
     * Включение логгирования
     *
     * @see ObjectStorage
     * @see FileClient::enableLog()
     */
    public static function enableLog(): void
    {
        FileClient::getInstance()->enableLog();
    }
}
