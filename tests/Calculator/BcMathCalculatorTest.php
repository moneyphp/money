<?php

declare(strict_types=1);

namespace Tests\Money\Calculator;

use Money\Calculator\BcMathCalculator;

use function bcscale;
use function ini_get;

/**
 * @requires extension bcmath
 */
class BcMathCalculatorTest extends CalculatorTestCase
{
    private int $defaultScale;

    protected function getCalculator(): BcMathCalculator
    {
        return new BcMathCalculator();
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
     * @psalm-param positive-int $value1
     * @psalm-param positive-int $value2
     * @psalm-param numeric-string $expected
     *
     * @dataProvider additionExamples
     * @test
     */
    public function itAddsTwoValuesWithScaleSet(int $value1, int $value2, string $expected): void
    {
        bcscale(1);

        self::assertEqualNumber($expected, $this->getCalculator()->add((string) $value1, (string) $value2));
    }

    /**
     * @psalm-param positive-int $value1
     * @psalm-param positive-int $value2
     * @psalm-param numeric-string $expected
     *
     * @dataProvider subtractionExamples
     * @test
     */
    public function itSubtractsAValueFromAnotherWithScaleSet(int $value1, int $value2, string $expected): void
    {
        bcscale(1);

        self::assertEqualNumber($expected, $this->getCalculator()->subtract((string) $value1, (string) $value2));
    }

    /**
     * @test
     */
    public function itComparesNumbersCloseToZero(): void
    {
        $this->assertEquals(1, $this->getCalculator()->compare('1', '0.0005'));
        $this->assertEquals(1, $this->getCalculator()->compare('1', '0.000000000000000000000000005'));
    }

    /**
     * @test
     */
    public function itUsesScaleForAdd(): void
    {
        $this->assertEquals('0.00130154000000', $this->getCalculator()->add('0.00125148', '0.00005006'));
    }

    /**
     * @test
     */
    public function itUsesScaleForSubtract(): void
    {
        self::assertEqualNumber('0.00120142', $this->getCalculator()->subtract('0.00125148', '0.00005006'));
    }
}
