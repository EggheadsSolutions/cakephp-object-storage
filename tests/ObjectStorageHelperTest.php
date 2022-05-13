<?php
declare(strict_types=1);

namespace Eggheads\CakephpObjectStorage\Tests;

use Cake\TestSuite\TestCase;
use Eggheads\CakephpObjectStorage\ObjectStorageHelper;
use Eggheads\Mocks\MethodMocker;
use Exception;

class ObjectStorageHelperTest extends TestCase
{
    /**
     * Данные для теста testGetName
     *
     * @return iterable<array<string|int|bool|null>>
     */
    public function dataProviderTestGetName(): iterable
    {
        yield 'Передано всё' => ['filename.txt', 'prefix', 3, true, '3/prefix/filename.txt'];
        yield 'Пропустили префикс' => ['filename.txt', null, 3, true, '3/filename.txt'];
        yield 'Пропустили ИД' => ['filename.txt', 'prefix', null, true, 'prefix/filename.txt'];
        yield 'Пропустили префикс и ИД' => ['filename.txt', null, null, true, 'filename.txt'];
        yield 'Файл без расширения' => ['filename', 'prefix', 3, true, '3/prefix/filename'];
        yield 'Кусок пути, вместо файла' => ['/foo/bar/filename.txt', 'prefix', null, true, 'prefix/filename.txt'];
        yield 'Добавлять случайную примесь' => ['filename.txt', 'prefix', 3, false, '3/prefix/filename12345.txt'];
    }

    /**
     * Тестируем getName
     *
     * @param string $filename
     * @param string|null $prefix
     * @param int|null $id
     * @param bool $isSkipUniqueLabel
     * @param string $key
     * @return void
     * @throws Exception
     * @see          ObjectStorageHelper::getKey()
     * @dataProvider dataProviderTestGetName
     */
    public function testGetName(string $filename, ?string $prefix, ?int $id, bool $isSkipUniqueLabel, string $key): void
    {
        if (!$isSkipUniqueLabel) {
            MethodMocker::mock(ObjectStorageHelper::class, '_uniqueLabel')
                ->singleCall()
                ->willReturnValue('12345');
        }

        self::assertEquals($key, ObjectStorageHelper::getKey($filename, $prefix, $id, $isSkipUniqueLabel));
    }
}
