<?php

declare(strict_types=1);

namespace Tests\Money\Calculator;

use Money\Calculator\BcMathCalculator;
use Money\Exception\InvalidArgumentException;

use function array_merge;
use function bcscale;
use function ini_get;

/**
 * @requires extension bcmath
 * @covers \Money\Calculator\BcMathCalculator
 */
class BcMathCalculatorTest extends CalculatorTestCase
{
    private int $defaultScale;

    /**
     * @return BcMathCalculator
     * @phpstan-return class-string<BcMathCalculator>
     */
    protected function getCalculator(): string
    {
        return BcMathCalculator::class;
    }

    public function setUp(): void
    {
        $this->defaultScale = (int) ini_get('bcmath.scale');
    }

    public function tearDown(): void
    {
        bcscale($this->defaultScale);
    }

    /**
     * @phpstan-param positive-int   $value1
     * @phpstan-param positive-int   $value2
     * @phpstan-param numeric-string $expected
     *
     * @dataProvider additionExamples
     * @test
     */
    public function itAddsTwoValuesWithScaleSet(int $value1, int $value2, string $expected): void
    {
        bcscale(1);

        self::assertEqualNumber($expected, $this->getCalculator()::add((string) $value1, (string) $value2));
    }

    /**
     * @phpstan-param positive-int   $value1
     * @phpstan-param positive-int   $value2
     * @phpstan-param numeric-string $expected
     *
     * @dataProvider subtractionExamples
     * @test
     */
    public function itSubtractsAValueFromAnotherWithScaleSet(int $value1, int $value2, string $expected): void
    {
        bcscale(1);

        self::assertEqualNumber($expected, $this->getCalculator()::subtract((string) $value1, (string) $value2));
    }

    /**
     * @test
     */
    public function itComparesNumbersCloseToZero(): void
    {
        self::assertEquals(1, $this->getCalculator()::compare('1', '0.0005'));
        self::assertEquals(1, $this->getCalculator()::compare('1', '0.000000000000000000000000005'));
    }

    /**
     * @test
     */
    public function itUsesScaleForAdd(): void
    {
        self::assertEquals('0.00130154000000', $this->getCalculator()::add('0.00125148', '0.00005006'));
    }

    /**
     * @test
     */
    public function itUsesScaleForSubtract(): void
    {
        self::assertEqualNumber('0.00120142', $this->getCalculator()::subtract('0.00125148', '0.00005006'));
    }

    /** @test */
    public function itRefusesToDivideByZeroWhenDivisorIsTooSmallToCompare(): void
    {
        $calculator = $this->getCalculator();

        $this->expectException(InvalidArgumentException::class);

        $calculator::divide('1', '0.0000000000000000000000000000000000000000001');
    }

    /** @test */
    public function itRefusesToModuloByZeroWhenDivisorIsTooSmallToCompare(): void
    {
        $calculator = $this->getCalculator();

        $this->expectException(InvalidArgumentException::class);

        $calculator::mod('1', '0.0000000000000000000000000000000000000000001');
    }

    /**
     * @phpstan-return array<int,array<int|numeric-string>>
     */
    public static function compareLessExamples(): array
    {
        return array_merge(
            parent::compareLessExamples(),
            [
                // Slightly below PHP_INT_MIN on 64 bit systems (does not work with the PhpCalculator)
                ['-9223372036854775810', '-9223372036854775809', -1],
            ]
        );
    }
}
