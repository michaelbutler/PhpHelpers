<?php declare(strict_types=1);

namespace Butler\PhpHelpers;

class MoneyUtil
{
    /**
     * Converts an integer cents such as 4750 to a human readable display string like "$47.50"
     * @param int $cents Number of cents
     * @param string $sign Dollar sign. Can be a blank string to omit.
     * @return string
     */
    public static function centsToDollars(int $cents, string $sign = '$'): string
    {
        return $sign . number_format(round($cents / 100, 4), 2, '.', '');
    }
}
