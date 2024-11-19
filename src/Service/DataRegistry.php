<?php
namespace App\Service;
abstract class DataRegistry
{
    private static array $data = [];
    public static function setData(array $data): void
    {
        self::$data = $data;
    }
    public static function getData(): array
    {
        return self::$data;
    }
}
