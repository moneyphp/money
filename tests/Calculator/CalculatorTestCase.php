<?php

declare(strict_types=1);

namespace Tests\Money\Calculator;

use Money\Calculator;
use Money\Exception\InvalidArgumentException;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Tests\Money\Locale;
use Tests\Money\RoundExamples;

use function preg_replace;
use function rtrim;
use function substr;

use const LC_ALL;

abstract class CalculatorTestCase extends TestCase
{
    use RoundExamples;
    use Locale;

    /**
     * @return Calculator
     * @phpstan-return class-string<Calculator>
     */
    abstract protected function getCalculator(): string;

    /**
     * @phpstan-param positive-int $value1
     * @phpstan-param positive-int $value2
     * @phpstan-param numeric-string $expected
     *
     * @dataProvider additionExamples
     * @test
     */
    public function itAddsTwoValues(int $value1, int $value2, string $expected): void
    {
        self::assertEqualNumber($expected, $this->getCalculator()::add((string) $value1, (string) $value2));

        self::runLocaleAware(LC_ALL, 'ru_RU.UTF-8', function () use ($value1, $value2, $expected): void {
            self::assertEqualNumber($expected, $this->getCalculator()::add((string) $value1, (string) $value2));
        });
    }

    /**
     * @phpstan-param positive-int $value1
     * @phpstan-param positive-int $value2
     * @phpstan-param numeric-string $expected
     *
     * @dataProvider subtractionExamples
     * @test
     */
    public function itSubtractsAValueFromAnother(int $value1, int $value2, string $expected): void
    {
        self::assertEqualNumber($expected, $this->getCalculator()::subtract((string) $value1, (string) $value2));

        self::runLocaleAware(LC_ALL, 'ru_RU.UTF-8', function () use ($value1, $value2, $expected): void {
            self::assertEqualNumber($expected, $this->getCalculator()::subtract((string) $value1, (string) $value2));
        });
    }

    /**
     * @phpstan-param positive-int|numeric-string $value1
     * @phpstan-param float $value2
     * @phpstan-param numeric-string $expected
     *
     * @dataProvider multiplicationExamples
     * @test
     */
    public function itMultipliesAValueByAnother(int|string $value1, float $value2, string $expected): void
    {
        self::assertEqualNumber($expected, $this->getCalculator()::multiply((string) $value1, (string) $value2));

        self::runLocaleAware(LC_ALL, 'ru_RU.UTF-8', function () use ($value1, $value2, $expected): void {
            self::assertEqualNumber($expected, $this->getCalculator()::multiply((string) $value1, (string) $value2));
        });
    }

    /**
     * @phpstan-param positive-int|numeric-string $value1
     * @phpstan-param positive-int|float $value2
     * @phpstan-param numeric-string $expected
     *
     * @dataProvider divisionExamples
     * @test
     */
    public function itDividesAValueByAnother(int|string $value1, int|float $value2, string $expected): void
    {
        $expectedNumericString = substr($expected, 0, 12);
        $resultNumericString   = substr(
            $this->getCalculator()::divide((string) $value1, (string) $value2),
            0,
            12
        );

        self::assertIsNumeric($expectedNumericString);
        self::assertIsNumeric($resultNumericString);
        self::assertEqualNumber($expectedNumericString, $resultNumericString);
    }

    /**
     * @phpstan-param positive-int $value1
     * @phpstan-param positive-int|float $value2
     * @phpstan-param numeric-string $expected
     *
     * @dataProvider divisionExactExamples
     * @test
     */
    public function itDividesAValueByAnotherExact(int $value1, int|float $value2, string $expected): void
    {
        self::assertEqualNumber($expected, $this->getCalculator()::divide((string) $value1, (string) $value2));

        self::runLocaleAware(LC_ALL, 'ru_RU.UTF-8', function () use ($value1, $value2, $expected): void {
            self::assertEqualNumber($expected, $this->getCalculator()::divide((string) $value1, (string) $value2));
        });
    }

    /**
     * @phpstan-param float $value
     * @phpstan-param numeric-string $expected
     *
     * @dataProvider ceilExamples
     * @test
     */
    public function itCeilsAValue(float $value, string $expected): void
    {
        self::assertEquals($expected, $this->getCalculator()::ceil((string) $value));

        self::runLocaleAware(LC_ALL, 'ru_RU.UTF-8', function () use ($value, $expected): void {
            self::assertEqualNumber($expected, $this->getCalculator()::ceil((string) $value));
        });
    }

    /**
     * @phpstan-param float $value
     * @phpstan-param numeric-string $expected
     *
     * @dataProvider floorExamples
     * @test
     */
    public function itFloorsAValue(float $value, string $expected): void
    {
        self::assertEquals($expected, $this->getCalculator()::floor((string) $value));

        self::runLocaleAware(LC_ALL, 'ru_RU.UTF-8', function () use ($value, $expected): void {
            self::assertEqualNumber($expected, $this->getCalculator()::floor((string) $value));
        });
    }

    /**
     * @phpstan-param int $value
     * @phpstan-param numeric-string $expected
     *
     * @dataProvider absoluteExamples
     * @test
     */
    public function itCalculatesTheAbsoluteValue(int $value, string $expected): void
    {
        self::assertEquals($expected, $this->getCalculator()::absolute((string) $value));

        self::runLocaleAware(LC_ALL, 'ru_RU.UTF-8', function () use ($value, $expected): void {
            self::assertEqualNumber($expected, $this->getCalculator()::absolute((string) $value));
        });
    }

    /**
     * @phpstan-param int $value
     * @phpstan-param int $ratio
     * @phpstan-param int $total
     * @phpstan-param numeric-string $expected
     *
     * @dataProvider shareExamples
     * @test
     */
    public function itSharesAValue(int $value, int $ratio, int $total, string $expected): void
    {
        self::assertEquals($expected, $this->getCalculator()::share((string) $value, (string) $ratio, (string) $total));
    }

    /**
     * @phpstan-param int|numeric-string $value
     * @phpstan-param Money::ROUND_* $mode
     * @phpstan-param numeric-string $expected
     *
     * @dataProvider roundingExamples
     * @test
     */
    public function itRoundsAValue(int|string $value, int $mode, string $expected): void
    {
        self::assertEquals($expected, $this->getCalculator()::round((string) $value, $mode));

        self::runLocaleAware(LC_ALL, 'ru_RU.UTF-8', function () use ($value, $mode, $expected): void {
            self::assertEqualNumber($expected, $this->getCalculator()::round((string) $value, $mode));
        });
    }

    /**
     * @phpstan-param int|numeric-string $left
     * @phpstan-param int|numeric-string $right
     *
     * @dataProvider compareLessExamples
     * @test
     */
    public function itComparesValuesLess(int|string $left, int|string $right): void
    {
        // Compare with both orders. One must return a value less than zero,
        // the other must return a value greater than zero.
        self::assertLessThan(0, $this->getCalculator()::compare((string) $left, (string) $right));
        self::assertGreaterThan(0, $this->getCalculator()::compare((string) $right, (string) $left));
    }

    /**
     * @phpstan-param int|numeric-string $left
     * @phpstan-param int|numeric-string $right
     *
     * @dataProvider compareEqualExamples
     * @test
     */
    public function itComparesValues(int|string $left, int|string $right): void
    {
        // Compare with both orders, both must return zero.
        self::assertEquals(0, $this->getCalculator()::compare((string) $left, (string) $right));
        self::assertEquals(0, $this->getCalculator()::compare((string) $left, (string) $right));
    }

    /**
     * @phpstan-param int $left
     * @phpstan-param int $right
     * @phpstan-param numeric-string $expected
     *
     * @dataProvider modExamples
     * @test
     */
    public function itCalculatesTheModulusOfAValue(int $left, int $right, string $expected): void
    {
        self::assertEquals($expected, $this->getCalculator()::mod((string) $left, (string) $right));

        self::runLocaleAware(LC_ALL, 'ru_RU.UTF-8', function () use ($left, $right, $expected): void {
            self::assertEqualNumber($expected, $this->getCalculator()::mod((string) $left, (string) $right));
        });
    }

    /** @test */
    public function itRefusesToDivideByZero(): void
    {
        $calculator = $this->getCalculator();

        $this->expectException(InvalidArgumentException::class);

        $calculator::divide('1', '0');
    }

    /** @test */
    public function itRefusesToDivideByNegativeZero(): void
    {
        $calculator = $this->getCalculator();

        $this->expectException(InvalidArgumentException::class);

        $calculator::divide('1', '-0');
    }

    /** @test */
    public function itRefusesToModuloByZero(): void
    {
        $calculator = $this->getCalculator();

        $this->expectException(InvalidArgumentException::class);

        $calculator::mod('1', '0');
    }

    /** @test */
    public function itRefusesToModuloByNegativeZero(): void
    {
        $calculator = $this->getCalculator();

        $this->expectException(InvalidArgumentException::class);

        $calculator::mod('1', '-0');
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     positive-int,
     *     positive-int,
     *     numeric-string
     * }>
     */
    public static function additionExamples(): array
    {
        return [
            [1, 1, '2'],
            [10, 5, '15'],
        ];
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     positive-int,
     *     positive-int,
     *     numeric-string
     * }>
     */
    public static function subtractionExamples(): array
    {
        return [
            [1, 1, '0'],
            [10, 5, '5'],
        ];
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     positive-int|numeric-string,
     *     float,
     *     numeric-string
     * }>
     */
    public static function multiplicationExamples(): array
    {
        return [
            [1, 1.5, '1.5'],
            [10, 1.2500, '12.50'],
            [100, 0.29, '29'],
            [100, 0.029, '2.9'],
            [100, 0.0029, '0.29'],
            [1000, 0.29, '290'],
            [1000, 0.029, '29'],
            [1000, 0.0029, '2.9'],
            [2000, 0.0029, '5.8'],
            ['1', 0.006597, '0.006597'],
            ['1', -0.99, '-0.99'],
            ['1', -1.99, '-1.99'],
            ['-1', -1.99, '1.99'],
        ];
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     positive-int|numeric-string,
     *     positive-int|float,
     *     numeric-string
     * }>
     */
    public static function divisionExamples(): array
    {
        return [
            [6, 3, '2'],
            [100, 25, '4'],
            [2, 4, '0.5'],
            [20, 0.5, '40'],
            [2, 0.5, '4'],
            [181, 17, '10.64705882352941'],
            [98, 28, '3.5'],
            [98, 25, '3.92'],
            [98, 24, '4.083333333333333'],
            [1, 5.1555, '0.19396760740956'],
            ['-500', 110, '-4.54545454545455'],
            ['1', -0.99, '-1.0101010101'],
            ['-1', -0.99, '1.0101010101'],
            ['-1', -1.99, '0.5025125628'],
        ];
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     positive-int,
     *     positive-int|float,
     *     numeric-string
     * }>
     */
    public static function divisionExactExamples(): array
    {
        return [
            [6, 3, '2'],
            [100, 25, '4'],
            [2, 4, '0.5'],
            [20, 0.5, '40'],
            [2, 0.5, '4'],
            [98, 28, '3.5'],
            [98, 25, '3.92'],
        ];
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     float,
     *     numeric-string
     * }>
     */
    public static function ceilExamples(): array
    {
        return [
            [1.2, '2'],
            [-1.2, '-1'],
            [2.00, '2'],
        ];
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     float,
     *     numeric-string
     * }>
     */
    public static function floorExamples(): array
    {
        return [
            [2.7, '2'],
            [-2.7, '-3'],
            [2.00, '2'],
        ];
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     int,
     *     numeric-string
     * }>
     */
    public static function absoluteExamples(): array
    {
        return [
            [2, '2'],
            [-2, '2'],
        ];
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     int,
     *     int,
     *     int,
     *     numeric-string
     * }>
     */
    public static function shareExamples(): array
    {
        return [
            [10, 2, 4, '5'],
        ];
    }

    /**
     * @phpstan-return array<int,array<int|numeric-string>>
     */
    public static function compareLessExamples(): array
    {
        return [
            [0, 1],
            ['0', '1'],
            ['0.0005', '1'],
            ['0.000000000000000000000000005', '1'],
            ['-1000', '1000', -1],
            // Slightly above PHP_INT_MAX on 64 bit systems
            ['9223372036854775808', '9223372036854775809', -1],
        ];
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     int|numeric-string,
     *     int|numeric-string
     * }>
     */
    public static function compareEqualExamples(): array
    {
        return [
            [1, 1],
            ['1', '1'],
            ['-1000', '-1000'],
        ];
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     int,
     *     int,
     *     numeric-string
     * }>
     */
    public static function modExamples(): array
    {
        return [
            [11, 5, '1'],
            [9, 3, '0'],
            [1006, 10, '6'],
            [1007, 10, '7'],
            [-13, -5, '-3'],
            [-13, 5, '-3'],
            [13, -5, '3'],
        ];
    }

    /**
     * Fixed point precision operations sometimes retrieve trailing zeroes due to higher precision than requested:
     * this is acceptable for us, and we are OK with ignoring trailing zero fractional digits during test comparisons.
     *
     * @phpstan-param numeric-string $expected
     * @phpstan-param numeric-string $result
     */
    final protected static function assertEqualNumber(string $expected, string $result): void
    {
        $normalizedExpected = $expected;
        $normalizedResult   = $result;

        // Thank you, Murica -.-
        if ($normalizedExpected[0] === '.') {
            $normalizedExpected = '0' . $normalizedExpected;
        }

        if ($normalizedResult[0] === '.') {
            $normalizedResult = '0' . $normalizedResult;
        }

        $normalizedExpected = rtrim(preg_replace('/^(-?\d+\.\d*?[1-9]*)0+$/', '$1', $normalizedExpected), '.');
        $normalizedResult   = rtrim(preg_replace('/^(-?\d+\.\d*?[1-9]*)0+$/', '$1', $normalizedResult), '.');

        self::assertEquals($normalizedExpected, $normalizedResult);
    }
}
