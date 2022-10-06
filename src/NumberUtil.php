<?php declare(strict_types=1);

namespace Butler\PhpHelpers;

class NumberUtil
{
    /**
     * Checks if input string is a number, either whole or floating point.
     * Binary, Octal, Hex and scientific notation is not allowed. Only checks for
     * typical number formats.
     * @param int|float|string $str
     */
    public static function isNumber($str): bool
    {
        if (is_int($str) || is_float($str)) {
            return true;
        }
        if (!is_string($str)) {
            return false;
        }
        return (bool) preg_match('~^[+-]?([0-9]*[.])?[0-9]+$~', $str);
    }

    public static function toFloat($str): float
    {
        if (!self::isNumber($str)) {
            throw new \InvalidArgumentException('Cannot convert to float');
        }
        return (float) $str;
    }

    public function toInt($str): int
    {
        if (!self::isNumber($str)) {
            throw new \InvalidArgumentException('Cannot convert to integer');
        }
        return (int) $str;
    }
}
