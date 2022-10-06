<?php

namespace Test\Butler\PhpHelpers;

use Butler\PhpHelpers\NumberUtil;
use PHPUnit\Framework\TestCase;

class NumberUtilTest extends TestCase
{
    public function providerForIsNumber(): array
    {
        return [
            ['1.25', true],
            ["\0\0 I have null bytes but who cares!", false],
            ["🧟 Zombie 🧟 Zombie", false],
            [12345, true],
            [5.728, true],
            ["0", true],
            ["-1.5", true],
            ["-1.", false], // Ending with a period not accepted
            ["+1.5", true],
            ["5.72 ", false],
            [" 5.72", false],
            ["5.728f", false],
            ["1.07e58", false],
            [[3], false],
            [null, false],
            [true, false],
        ];
    }

    /**
     * @dataProvider providerForIsNumber
     * @param mixed $inputData
     * @param bool $expectedBool
     */
    public function testIsNumber($inputData, bool $expectedBool): void
    {
        $this->assertSame($expectedBool, NumberUtil::isNumber($inputData));
    }

    public function providerForToInt(): array
    {
        return [
            ['1.25', 1],
            ['6', 6],
            [6, 6],
            [7.992, 7],
        ];
    }

    /**
     * @dataProvider providerForToInt
     * @param mixed $inputData
     * @param int $expectedResult
     */
    public function testToInt($inputData, int $expectedResult): void
    {
        $this->assertSame($expectedResult, NumberUtil::toInt($inputData));
    }

    public function providerForToFloat(): array
    {
        return [
            ['1.25', 1.25],
            ['+5.6767', 5.6767],
            ['-8.999', -8.999],
            ['6', 6],
            [6, 6],
            [7.992, 7.992],
            ['1234.98473', 1234.98473],
        ];
    }

    /**
     * @dataProvider providerForToFloat
     * @param mixed $inputData
     * @param float $expectedResult
     */
    public function testToFloat($inputData, float $expectedResult): void
    {
        $this->assertSame($expectedResult, NumberUtil::toFloat($inputData));
    }

    public function testExceptionOnInvalidToInt(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        NumberUtil::toInt([]);
    }

    public function testExceptionOnInvalidToFloat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        NumberUtil::toFloat([]);
    }
}
