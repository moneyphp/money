<?php

declare(strict_types=1);

namespace Tests\Money;

use Money\Money;
use Money\PHPUnit\Comparator;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;

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

        $this->assertFalse($this->comparator->accepts($money_a, false));
        $this->assertFalse($this->comparator->accepts(false, $money_a));
        $this->assertTrue($this->comparator->accepts($money_a, $money_b));
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
            $this->assertSame('Failed asserting that two Money objects are equal.', $e->getMessage());
            $this->assertStringContainsString(
                '--- Expected
+++ Actual
@@ @@
-€0.01
+$0.01',
                $e->getDiff()
            );

            return;
        }

        $this->fail('ComparisonFailure should have been thrown.');
    }

    /**
     * @test
     */
    public function itComparesEqualValues(): void
    {
        $money_a = Money::EUR(1);
        $money_b = Money::EUR(1);

        $this->comparator->assertEquals($money_a, $money_b);

        $this->assertEquals(
            $money_a,
            $money_b,
            'This is only here to increment the assertion counter, since we are testing an assertion'
        );
    }
}
