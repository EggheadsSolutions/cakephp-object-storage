<?php
declare(strict_types=1);

namespace Eggheads\CakephpObjectStorage\Lib;

use Eggheads\CakephpObjectStorage\Traits\Library;

class Dir
{
    use Library;

    /**
     * Создание директории, если не существует
     *
     * @param string $filePath
     * @return bool
     */
    public static function createDir(string $filePath): bool
    {
        return is_dir($filePath)
            || mkdir($filePath, 0755, true)
            || is_dir($filePath);
    }
}
