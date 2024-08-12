<?php

namespace App\Enum;

use App\Services\Log;

trait EnumExtend
{
    public static function keys(): array
    {
        return array_map(function ($item) {
            return $item->name;
        }, self::cases());
    }

    public static function keyValues(): array
    {
        $result = [];
        foreach (self::cases() as $item) {
            $result[$item->name] = $item->value;
        }
        return $result;
    }

    public static function values(): array
    {
        return array_map(function ($item) {
            return $item->value;
        }, self::cases());
    }

    public function equal(string $value): bool
    {
        return self::tryFrom($value) === $this;
    }

    public static function include(string $value): bool
    {
        return in_array($value, self::keys());
    }

    public static function includeAll(array $names): bool
    {
        $keys = self::keys();
        foreach ($names as $name) {
            if (!in_array($name, $keys)) {
                return false;
            }
        }
        return true;
    }

    public static function getFrom(string $code)
    {
        $enum = self::tryFrom($code);
        if (!$enum) Log::error('Empty enum by code ' . $code);
        return $enum;
    }
}
