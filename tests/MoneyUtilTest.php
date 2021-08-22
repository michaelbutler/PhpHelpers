<?php declare(strict_types=1);

namespace Test\Butler\PhpHelpers;

use Butler\PhpHelpers\MoneyUtil;
use PHPUnit\Framework\TestCase;

class MoneyUtilTest extends TestCase
{
    public function providerForCentsToDollar(): array
    {
        return [
            [1537, '$', '$15.37'],
            [0, '$', '$0.00'],
            [1, '', '0.01'],
            [-999, '&', '&-9.99'],
        ];
    }

    /**
     * @dataProvider providerForCentsToDollar
     * @param $input
     * @param $sign
     * @param $expected
     */
    public function testCentsToDollars($input, $sign, $expected): void
    {
        $this->assertSame($expected, MoneyUtil::centsToDollars($input, $sign));
    }
}
