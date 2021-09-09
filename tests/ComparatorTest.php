<?php

declare(strict_types=1);

namespace Tests\Money;

use Money\Money;
use Money\PHPUnit\Comparator;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;

/** @covers \Money\PHPUnit\Comparator */
final class ComparatorTest extends TestCase
{
    protected Comparator $comparator;

    protected function setUp(): void
    {
        $this->comparator = new Comparator();
    }

    /**
     * @test
     */
    public function itAcceptsOnlyMoney(): void
    {
        $money_a = Money::EUR(1);
        $money_b = Money::EUR(2);

        self::assertFalse($this->comparator->accepts($money_a, false));
        self::assertFalse($this->comparator->accepts(false, $money_a));
        self::assertTrue($this->comparator->accepts($money_a, $money_b));
    }

    /**
     * @test
     */
    public function itComparesUnequalValues(): void
    {
        $money_a = Money::EUR(1);
        $money_b = Money::USD(1);

        try {
            $this->comparator->assertEquals($money_a, $money_b);
        } catch (ComparisonFailure $e) {
            self::assertSame('Failed asserting that two Money objects are equal.', $e->getMessage());
            self::assertStringContainsString(
                '--- Expected
+++ Actual
@@ @@
-€0.01
+$0.01',
                $e->getDiff()
            );

            return;
        }

        self::fail('ComparisonFailure should have been thrown.');
    }

    /**
     * @test
     */
    public function itComparesEqualValues(): void
    {
        $money_a = Money::EUR(1);
        $money_b = Money::EUR(1);

        $this->comparator->assertEquals($money_a, $money_b);

        self::assertEquals(
            $money_a,
            $money_b,
            'This is only here to increment the assertion counter, since we are testing an assertion'
        );
    }
}
