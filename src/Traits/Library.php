<?php // phpcs:ignore

namespace Eggheads\CakephpObjectStorage\Traits;

trait Library
{
    /**
     * Защищаем от создания через new
     */
    public function __construct()
    {
    }

    /**
     * Защищаем от создания через клонирование
     */
    private function __clone()
    {
    }

    /**
     * Защищаем от создания через unserialize
     */
    public function __wakeup()
    {
    }
}
