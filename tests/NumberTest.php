<?php

declare(strict_types=1);

namespace Tests\Money;

use InvalidArgumentException;
use Money\Number;
use PHPUnit\Framework\TestCase;

use function str_repeat;
use function strlen;
use function substr;

use const PHP_INT_MAX;

/** @covers \Money\Number */
final class NumberTest extends TestCase
{
    /**
     * @phpstan-param numeric-string $number
     * @phpstan-param numeric-string $integerPart
     * @phpstan-param string $fractionalPart
     *
     * @dataProvider numberExamples
     * @test
     */
    public function itHasAttributes(string $number, bool $decimal, bool $half, bool $currentEven, bool $negative, string $integerPart, string $fractionalPart): void
    {
        $number = Number::fromString($number);

        self::assertSame($decimal, $number->isDecimal());
        self::assertSame($half, $number->isHalf());
        self::assertSame($currentEven, $number->isCurrentEven());
        self::assertSame($negative, $number->isNegative());
        self::assertSame($integerPart, $number->getIntegerPart());
        self::assertSame($fractionalPart, $number->getFractionalPart());
        self::assertSame($negative ? '-1' : '1', $number->getIntegerRoundingMultiplier());
    }

    /**
     * @dataProvider invalidNumberExamples
     * @test
     */
    public function itFailsParsingInvalidNumbers(string $number): void
    {
        $this->expectException(InvalidArgumentException::class);

        // @phpstan-ignore staticMethod.resultUnused
        Number::fromString($number);
    }

    /**
     * @phpstan-param numeric-string $numberString
     * @phpstan-param numeric-string $expectedResult
     *
     * @dataProvider base10Examples
     * @test
     */
    public function base10(string $numberString, int $baseNumber, string $expectedResult): void
    {
        $number = Number::fromString($numberString);

        self::assertSame($expectedResult, (string) $number->base10($baseNumber));
    }

    /**
     * @phpstan-param int|numeric-string $number
     *
     * @dataProvider numericExamples
     * @test
     */
    public function itCreatesANumberFromANumericValue(int|string $number): void
    {
        $number = Number::fromNumber($number);

        self::assertInstanceOf(Number::class, $number);
    }

    /** @test */
    public function itCreatesANumberFromAFloatingPointValue(): void
    {
        self::assertEquals(
            Number::fromString('123.456789'),
            Number::fromFloat(123.456789)
        );
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     numeric-string,
     *     bool,
     *     bool,
     *     bool,
     *     bool,
     *     numeric-string,
     *     string
     * }>
     *
     *
     * the {@see PHP_INT_MAX} operations below cannot be inferred to numeric-string
     * the {@see PHP_INT_MAX} operations below cannot be inferred to numeric-string
     * concatenation of {@see PHP_INT_MAX} is disallowed by type checker, but valid in this scenario
     */
    public static function numberExamples(): array
    {
        return [
            ['0', false, false, true, false, '0', ''],
            ['0.00', false, false, true, false, '0', ''],
            ['0.5', true, true, true, false, '0', '5'],
            ['0.500', true, true, true, false, '0', '5'],
            ['-0', false, false, true, true, '-0', ''],
            ['-0.5', true, true, true, true, '-0', '5'],
            ['3', false, false, false, false, '3', ''],
            ['3.00', false, false, false, false, '3', ''],
            ['3.5', true, true, false, false, '3', '5'],
            ['3.500', true, true, false, false, '3', '5'],
            ['-3', false, false, false, true, '-3', ''],
            ['-3.5', true, true, false, true, '-3', '5'],
            ['10', false, false, true, false, '10', ''],
            ['10.00', false, false, true, false, '10', ''],
            ['10.5', true, true, true, false, '10', '5'],
            ['10.500', true, true, true, false, '10', '5'],
            ['10.9', true, false, true, false, '10', '9'],
            ['-10', false, false, true, true, '-10', ''],
            ['-0', false, false, true, true, '-0', ''],
            ['-10.5', true, true, true, true, '-10', '5'],
            ['-.5', true, true, true, true, '-0', '5'],
            ['.5', true, true, true, false, '0', '5'],
            [(string) PHP_INT_MAX, false, false, false, false, (string) PHP_INT_MAX, ''],
            [(string) -PHP_INT_MAX, false, false, false, true, (string) -PHP_INT_MAX, ''],
            [
                PHP_INT_MAX . PHP_INT_MAX . PHP_INT_MAX,
                false,
                false,
                false,
                false,
                PHP_INT_MAX . PHP_INT_MAX . PHP_INT_MAX,
                '',
            ],
            [
                -PHP_INT_MAX . PHP_INT_MAX . PHP_INT_MAX,
                false,
                false,
                false,
                true,
                -PHP_INT_MAX . PHP_INT_MAX . PHP_INT_MAX,
                '',
            ],
            [
                substr((string) PHP_INT_MAX, 0, strlen((string) PHP_INT_MAX) - 1) . str_repeat('0', strlen((string) PHP_INT_MAX) - 1) . PHP_INT_MAX,
                false,
                false,
                false,
                false,
                substr((string) PHP_INT_MAX, 0, strlen((string) PHP_INT_MAX) - 1) . str_repeat('0', strlen((string) PHP_INT_MAX) - 1) . PHP_INT_MAX,
                '',
            ],
        ];
    }

    /** @phpstan-return non-empty-list<array{string}> */
    public static function invalidNumberExamples(): array
    {
        return [
            [''],
            ['000'],
            ['005'],
            ['123456789012345678-123456'],
            ['---123'],
            ['123456789012345678+13456'],
            ['-123456789012345678.-13456'],
            ['+123456789'],
            ['+123456789012345678.+13456'],
            ['123.456.789'],
            ['123.456z'],
            ['123z'],
        ];
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     numeric-string,
     *     int,
     *     numeric-string
     * }>
     */
    public static function base10Examples(): array
    {
        return [
            ['0', 10, '0'],
            ['5', 1, '0.5'],
            ['50', 2, '0.5'],
            ['50', 3, '0.05'],
            ['0.5', 2, '0.005'],
            ['500', 2, '5'],
            ['500', 0, '500'],
            ['500', -2, '50000'],
            ['0.5', -2, '50'],
            ['0.5', -3, '500'],
            ['-5', 3, '-0.005'],
            ['-5', -3, '-5000'],
            ['-0.05', -3, '-50'],
            ['-0.5', -3, '-500'],
        ];
    }

    /** @phpstan-return non-empty-list<array{int|numeric-string}> */
    public static function numericExamples(): array
    {
        return [
            [1],
            [-1],
            ['1'],
            ['-1'],
            ['1.0'],
            ['-1.0'],
        ];
    }
}
