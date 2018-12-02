<?php

namespace Tests\Money;

use Money\PHPUnit\Comparator;
use Money\Money;
use PHPUnit\Framework\TestCase;

final class ComparatorTest extends TestCase
{
    /**
     * @var Comparator
     */
    protected $comparator;

    protected function setUp()
    {
        $this->comparator = new Comparator();
    }

    /**
     * @test
     */
    public function it_accepts_only_money()
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
    public function it_compares_unequal_values()
    {
        $money_a = Money::EUR(1);
        $money_b = Money::USD(1);

        try {
            $this->comparator->assertEquals($money_a, $money_b);
        } catch (\SebastianBergmann\Comparator\ComparisonFailure $e) {
            $this->assertEquals('Failed asserting that two Money objects are equal.', $e->getMessage());
            $this->assertContains(
                '--- Expected
+++ Actual
@@ @@
-€0.01
+$0.01', $e->getDiff()
            );

            return;
        }

        $this->fail('ComparisonFailure should have been thrown.');
    }

    /**
     * @test
     */
    public function it_compares_equal_values()
    {
        $money_a = Money::EUR(1);
        $money_b = Money::EUR(1);

        $this->assertNull($this->comparator->assertEquals($money_a, $money_b));
    }
}
