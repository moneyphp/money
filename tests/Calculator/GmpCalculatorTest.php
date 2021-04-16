<?php

declare(strict_types=1);

namespace Tests\Money\Calculator;

use Money\Calculator\GmpCalculator;

use function array_merge;

/**
 * @requires extension gmp
 */
class GmpCalculatorTest extends CalculatorTestCase
{
    /**
     * @return GmpCalculator
     * @psalm-return class-string<GmpCalculator>
     */
    protected function getCalculator(): string
    {
        return GmpCalculator::class;
    }

    /**
     * @test
     */
    public function itMultipliesZero(): void
    {
        $this->assertSame('0', $this->getCalculator()::multiply('0', '0.8'));
    }

    /**
     * @test
     */
    public function itFloorsZero(): void
    {
        $this->assertSame('0', $this->getCalculator()::floor('0'));
    }

    /**
     * @test
     */
    public function itComparesZeroWithFraction(): void
    {
        $this->assertSame(1, $this->getCalculator()::compare('0.5', '0'));
    }

    /**
     * @test
     */
    public function it_divides_bug538(): void
    {
        $this->assertSame('-4.54545454545455', $this->getCalculator()::divide('-500', '110'));
    }

    /**
     * @psalm-return non-empty-list<array{
     *     int|numeric-string,
     *     int|numeric-string
     * }>
     */
    public function compareLessExamples(): array
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
