<?php

namespace Test\Butler\PhpHelpers;

use Butler\PhpHelpers\StringUtil;
use PHPUnit\Framework\TestCase;

class StringUtilTest extends TestCase
{
    public function testGetRandomStringLength(): void
    {
        $randomString = StringUtil::getRandomString(1025);
        $this->assertSame(1025, strlen($randomString));
    }

    public function testGetRandomStringContainsNoAmbiguousChars(): void
    {
        for ($i = 0; $i < 1000; $i++) {
            $randomString = StringUtil::getRandomString(512);
            $this->assertEmpty(preg_match(StringUtil::AMBIGUOUS_CHARACTERS_REGEX, $randomString));
        }
    }

    public function providerForIsRawUtf8(): array
    {
        return [
            ['hello world', true],
            ["\0\0 I have null bytes but who cares!", true],
            ["🧟 Zombie 🧟 Zombie", true],
            ["this is latin1 stuff " . hex2bin('DF'), false],
            ["garbage " . hex2bin('0B'), true], // non-printable, but still valid ASCII
            ["garbage " . hex2bin('0E'), true], // non-printable, but still valid ASCII
        ];
    }

    /**
     * @dataProvider providerForIsRawUtf8
     * @param $inputString
     * @param $expectedBool
     */
    public function testIsRawUtf8($inputString, $expectedBool): void
    {
        $this->assertSame($expectedBool, StringUtil::isRawUtf8($inputString));
    }

    public function providerForIsTypicalUtf8(): array
    {
        return [
            ['hello world', true],
            ["\0\0 I have null bytes but who cares!", false],
            ["🧟 Zombie 🧟 Zombie", true],
            ["this is latin1 stuff " . hex2bin('DF'), false],
            ["garbage " . hex2bin('0B'), false],
            ["garbage " . hex2bin('0E'), false],
        ];
    }

    /**
     * @dataProvider providerForIsTypicalUtf8
     * @param $inputString
     * @param $expectedBool
     */
    public function testIsTypicalUtf8($inputString, $expectedBool): void
    {
        $this->assertSame($expectedBool, StringUtil::isTypicalUtf8($inputString));
    }

    public function providerForSlugify(): array
    {
        return [
            ["🧟 Zombie 🧟 Zombie", "zombie-zombie", []],
            ["🧟 Zombie 🧟 Zombie", "unicorn-unicorn", [
                'replacements' => [
                    '/zombie/i' => 'unicorn',
                ],
            ]],
            ["Really Long String haha wow", "really-long", [
                'limit' => 11,
            ]],
            ["HÉllo wőrld, fűn!", "hello-world-fun", []],
        ];
    }

    /**
     * @dataProvider providerForSlugify
     * @param $inputString
     * @param $expectedResult
     * @param array $options
     */
    public function testSlugify($inputString, $expectedResult, array $options): void
    {
        $this->assertSame($expectedResult, StringUtil::slugify($inputString, $options));
    }

    public function providerForToUtf8(): array
    {
        return [
            ["🧟 Zombie 🧟 Zombie", "🧟 Zombie 🧟 Zombie"],
            ["null char \0 works\n", "null char \0 works\n"],
            ["HÉllo wőrld, fűn!", "HÉllo wőrld, fűn!"],
            ["invalid char " . rawurldecode('%8F'), "invalid char "],
            [rawurldecode('%8F') . "=invalid char=", "=invalid char="],
        ];
    }

    /**
     * @dataProvider providerForToUtf8
     * @param $inputString
     * @param $expectedResult
     */
    public function testToUtf8($inputString, $expectedResult): void
    {
        $this->assertSame($expectedResult, StringUtil::toUtf8($inputString));
    }

    public function providerForIsValidEmail(): array
    {
        return [
            ["🧟 Zombie 🧟 Zombie", false],
            ["null char \0 works\n", false],
            ["HÉllo wőrld, fűn!", false],
            ["invalid char " . rawurldecode('%8F') . '@example.com', false],
            [rawurldecode('%8F') . "=invalid char=@example.com", false],
            ["_test_@example@.com", false],
            ["_tes%&*t_@example.n", false],
            ["white space here@test.net", false],

            ["test@example.com", true],
            ["_test_@example.com", true],
            ["_test_@example.com.x", true],
            ["_test_@example.com.", true],
            ["_tes%&*t_@example.com", true],
        ];
    }

    /**
     * @dataProvider providerForIsValidEmail
     * @param mixed $inputString
     * @param bool $expectedResult
     */
    public function testIsValidEmail($inputString, bool $expectedResult): void
    {
        $this->assertSame($expectedResult, StringUtil::isValidEmail($inputString));
    }
}
