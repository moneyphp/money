<?php

namespace Tests\Money;

use Money\Money;
use Money\Teller;

class TellerTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $this->teller = Teller::USD();
    }

    public function it_demonstrates_the_pennies_problem()
    {
        $amount1 = 1.23;
        $amount2 = 4.56;

        // this illustrates the problem with doing float
        // math on monetary values; among other things,
        // we end up with tenths of pennies.
        $actual = $amount1 * $amount2;
        $expect = 5.6088;
        $this->assertSame($expect, $actual);

        // instead, use the Teller to do monetary math.
        $actual = $this->teller->multiply($amount1, $amount2);
        $expect = '5.61';
        $this->assertSame($expect, $actual);
    }

    /**
     * @test
     */
    public function it_compares_equal_amounts()
    {
        $this->assertTrue($this->teller->equals('7.00', 7.00));
        $this->assertTrue($this->teller->equals('7', 7.00));
        $this->assertTrue($this->teller->equals(7, 7.00));
    }

    /**
     * @test
     */
    public function it_compares_two_amounts()
    {
        $amount = 1.23;
        $other = 4.56;

        $this->assertSame(-1, $this->teller->compare($amount, $other));
        $this->assertSame(0, $this->teller->compare($amount, $amount));
        $this->assertSame(+1, $this->teller->compare($other, $amount));
    }

    /**
     * @test
     */
    public function it_compares_greater_than_amounts()
    {
        $this->assertTrue($this->teller->greaterThan('45.67', '9.01'));
    }

    /**
     * @test
     */
    public function it_compares_greater_than_or_equal_amounts()
    {
        $this->assertTrue($this->teller->greaterThanOrEqual('45.67', '9.01'));
        $this->assertTrue($this->teller->greaterThanOrEqual('7.00', 7.00));
        $this->assertTrue($this->teller->greaterThanOrEqual('7', 7.00));
        $this->assertTrue($this->teller->greaterThanOrEqual(7, 7.00));
        $this->assertFalse($this->teller->greaterThanOrEqual(7, 7.01));
    }

    /**
     * @test
     */
    public function it_compares_less_than_amounts()
    {
        $this->assertTrue($this->teller->lessThan('9.01', '45.67'));
    }

    /**
     * @test
     */
    public function it_compares_less_than_or_equal_amounts()
    {
        $this->assertTrue($this->teller->lessThanOrEqual('9.01', '45.67'));
        $this->assertTrue($this->teller->lessThanOrEqual('7.00', 7.00));
        $this->assertTrue($this->teller->lessThanOrEqual('7', 7.00));
        $this->assertTrue($this->teller->lessThanOrEqual(7, 7.00));
        $this->assertFalse($this->teller->lessThanOrEqual(7, 6.99));
    }

    /**
     * @test
     */
    public function it_adds_amounts()
    {
        $actual = $this->teller->add(1.1, '2.2', 3, 4.44, '5.55');
        $expect = '16.29';
        $this->assertSame($expect, $actual);
    }

    /**
     * @test
     */
    public function it_subtracts_amounts()
    {
        $actual = $this->teller->subtract(1.1, '2.2', 3, 4.44, '5.55');
        $expect = '-14.09';
        $this->assertSame($expect, $actual);
    }

    /**
     * @test
     */
    public function it_multiplies_amounts()
    {
        $amount = 1.23;
        $multiplier = 4.56;

        $actual = $this->teller->multiply($amount, $multiplier);
        $expect = '5.61';
        $this->assertSame($expect, $actual);
    }

    /**
     * @test
     */
    public function it_multiplies_negative_amounts()
    {
        $amount = '-0.09';
        $multiplier = '0.01';
        $actual = $this->teller->multiply($amount, $multiplier);
        $expect = '0.00';
        $this->assertSame($expect, $actual);

        $amount = '-100.00';
        $multiplier = '0.01';
        $actual = $this->teller->multiply($amount, $multiplier);
        $expect = '-1.00';
        $this->assertSame($expect, $actual);

        $amount = '100.00';
        $multiplier = '-0.01';
        $actual = $this->teller->multiply($amount, $multiplier);
        $expect = '-1.00';
        $this->assertSame($expect, $actual);

        $amount = '141950.00';
        $multiplier = '-0.01';
        $actual = $this->teller->multiply($amount, $multiplier);
        $expect = '-1419.50';
        $this->assertSame($expect, $actual);

        $amount = '141950.00';
        $multiplier = '-0.01056710109193';
        $actual = $this->teller->multiply($amount, $multiplier);
        $expect = '-1500.00';
        $this->assertSame($expect, $actual);

        $amount = '141950.00';
        $multiplier = '-0.0001056710109193';
        $actual = $this->teller->multiply($amount, $multiplier);
        $expect = '-15.00';
        $this->assertSame($expect, $actual);
    }

    /**
     * @test
     */
    public function it_divides_amounts()
    {
        $amount = 1.23;
        $divisor = 4.56;

        $actual = $this->teller->divide($amount, $divisor);
        $expect = '0.27';
        $this->assertSame($expect, $actual);
    }

    /**
     * @test
     */
    public function it_divides_negative_amounts()
    {
        $amount = '-0.09';
        $divisor = '100';
        $actual = $this->teller->divide($amount, $divisor);
        $expect = '0.00';
        $this->assertSame($expect, $actual);

        $amount = '-100.00';
        $divisor = '0.01';
        $actual = $this->teller->divide($amount, $divisor);
        $expect = '-10000.00';
        $this->assertSame($expect, $actual);

        $amount = '100.00';
        $divisor = '-0.01';
        $actual = $this->teller->divide($amount, $divisor);
        $expect = '-10000.00';
        $this->assertSame($expect, $actual);

        $amount = '141950.00';
        $divisor = '-0.01056710109193';
        $actual = $this->teller->divide($amount, $divisor);
        $expect = '-13433201.67';
        $this->assertSame($expect, $actual);

        $amount = '141950.00';
        $divisor = '-0.0001056710109193';
        $actual = $this->teller->divide($amount, $divisor);
        $expect = '-1343320166.67';
        $this->assertSame($expect, $actual);
    }

    /**
     * @test
     */
    public function it_mods_amounts()
    {
        $amount = '10';
        $divisor = '3';
        $actual = $this->teller->mod($amount, $divisor);
        $expect = '1.00';
        $this->assertSame($expect, $actual);
    }

    /**
     * @test
     */
    public function it_allocates_amounts_across_ratios()
    {
        $amount = '100.00';
        $ratios = [1 / 2, 1 / 3, 1 / 6];
        $actual = $this->teller->allocate($amount, $ratios);
        $expect = [
            '50.00',
            '33.33',
            '16.67',
        ];
        $this->assertSame($expect, $actual);
    }

    /**
     * @test
     */
    public function it_allocates_amounts_among_targets()
    {
        $amount = '100.00';
        $n = 3;
        $actual = $this->teller->allocateTo($amount, $n);
        $expect = [
            '33.34',
            '33.33',
            '33.33',
        ];
        $this->assertSame($expect, $actual);
    }

    /**
     * @test
     */
    public function it_calculates_ratios_of_amounts()
    {
        $amount = '100.00';
        $other = '30';
        $actual = $this->teller->ratioOf($amount, $other);
        $expect = '3.33';
        $this->assertSame($expect, $actual);
    }

    /**
     * @test
     */
    public function it_calculates_absolute_amount()
    {
        $this->assertSame('7.00', $this->teller->absolute(-7));
        $this->assertSame('7.00', $this->teller->absolute(7.0));
    }

    /**
     * @test
     */
    public function it_calculates_negative_amount()
    {
        $this->assertSame('-7.00', $this->teller->negative(7));
        $this->assertSame('7.00', $this->teller->negative(-7));
    }

    /**
     * @test
     */
    public function it_compares_an_amount_to_zero()
    {
        $this->assertTrue($this->teller->isZero(0.00));
        $this->assertFalse($this->teller->isZero(0.01));
    }

    /**
     * @test
     */
    public function it_tells_if_an_amount_is_positive()
    {
        $this->assertTrue($this->teller->isPositive(1));
        $this->assertFalse($this->teller->isPositive(0));
        $this->assertFalse($this->teller->isPositive(-1));
    }

    /**
     * @test
     */
    public function it_tells_if_an_amount_is_negative()
    {
        $this->assertFalse($this->teller->isNegative(1));
        $this->assertFalse($this->teller->isNegative(0));
        $this->assertTrue($this->teller->isNegative(-1));
    }

    /**
     * @test
     */
    public function it_finds_the_minimum_amount()
    {
        $amounts = [
            '1.23',
            '4.56',
            '7.89',
            '0.12',
        ];

        $actual = $this->teller->min(...$amounts);
        $expect = '0.12';
        $this->assertSame($expect, $actual);
    }

    /**
     * @test
     */
    public function it_finds_the_maximum_amount()
    {
        $amounts = [
            '1.23',
            '4.56',
            '7.89',
            '0.12',
        ];

        $actual = $this->teller->max(...$amounts);
        $expect = '7.89';
        $this->assertSame($expect, $actual);
    }

    /**
     * @test
     */
    public function it_sums_amounts()
    {
        $amounts = [
            '1.23',
            '4.56',
            '7.89',
            '0.12',
        ];

        $actual = $this->teller->sum(...$amounts);
        $expect = '13.80';
        $this->assertSame($expect, $actual);
    }

    /**
     * @test
     */
    public function it_averages_amounts()
    {
        $amounts = [
            '1.23',
            '4.56',
            '7.89',
            '0.12',
        ];

        $actual = $this->teller->avg(...$amounts);
        $expect = '3.45';
        $this->assertSame($expect, $actual);
    }

    /**
     * @test
     */
    public function it_returns_a_zero_string()
    {
        $this->assertSame('0.00', $this->teller->zero());
    }

    /**
     * @test
     */
    public function it_converts_monetary_amounts()
    {
        $money = $this->teller->convertToMoney('1.23');
        $this->assertInstanceOf(Money::class, $money);
        $this->assertSame('123', $money->getAmount());
        $this->assertSame('USD', $money->getCurrency()->getCode());

        $value = $this->teller->convertToString($money);
        $this->assertSame('1.23', $value);
    }
}
