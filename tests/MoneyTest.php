<?php

namespace Tests\Money;

use Money\Currency;
use Money\Money;

final class MoneyTest extends \PHPUnit_Framework_TestCase
{
    use RoundExamples;

    const AMOUNT = 10;
    const OTHER_AMOUNT = 5;
    const CURRENCY = 'EUR';
    const OTHER_CURRENCY = 'USD';

    public function test_it_creates_money_using_factories()
    {
        $money = Money::XYZ(20);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals('20', $money->getAmount());
        $this->assertEquals('XYZ', $money->getCurrency()->getCode());
    }

    /**
     * @dataProvider equalityExamples
     * @test
     */
    public function it_equals_to_another_money($amount, $currency, $equality)
    {
        $money = new Money(self::AMOUNT, new Currency(self::CURRENCY));

        $this->assertEquals($equality, $money->equals(new Money($amount, $currency)));
    }

    /**
     * @dataProvider comparisonExamples
     * @test
     */
    public function it_compares_two_amounts($other, $result)
    {
        $money = new Money(self::AMOUNT, new Currency(self::CURRENCY));
        $other = new Money($other, new Currency(self::CURRENCY));

        $this->assertEquals($result, $money->compare($other));
        $this->assertEquals(1 === $result, $money->greaterThan($other));
        $this->assertEquals(0 <= $result, $money->greaterThanOrEqual($other));
        $this->assertEquals(-1 === $result, $money->lessThan($other));
        $this->assertEquals(0 >= $result, $money->lessThanOrEqual($other));
    }

    /**
     * @dataProvider roundExamples
     * @test
     */
    public function it_multiplies_the_amount($multiplier, $roundingMode, $result)
    {
        $money = new Money(1, new Currency(self::CURRENCY));

        $money = $money->multiply($multiplier, $roundingMode);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals((string) $result, $money->getAmount());
    }

    /**
     * @dataProvider invalidOperandExamples
     * @expectedException \InvalidArgumentException
     * @test
     */
    public function it_throws_an_exception_when_operand_is_invalid_during_multiplication($operand)
    {
        $money = new Money(1, new Currency(self::CURRENCY));

        $money->multiply($operand);
    }

    /**
     * @dataProvider roundExamples
     */
    public function it_divides_the_amount($divisor, $roundingMode, $result)
    {
        $money = new Money(1, new Currency(self::CURRENCY));

        $money = $money->divide(1 / $divisor, $roundingMode);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals((string) $result, $money->getAmount());
    }

    /**
     * @dataProvider invalidOperandExamples
     * @expectedException \InvalidArgumentException
     * @test
     */
    public function it_throws_an_exception_when_operand_is_invalid_during_division($operand)
    {
        $money = new Money(1, new Currency(self::CURRENCY));

        $money->divide($operand);
    }

    /**
     * @dataProvider allocationExamples
     * @test
     */
    public function it_allocates_amount($amount, $ratios, $results)
    {
        $money = new Money($amount, new Currency(self::CURRENCY));

        $allocated = $money->allocate($ratios);

        foreach ($allocated as $key => $money) {
            $compareTo = new Money($results[$key], $money->getCurrency());

            $this->assertTrue($money->equals($compareTo));
        }
    }

    /**
     * @dataProvider allocationTargetExamples
     * @test
     */
    public function it_allocates_amount_to_n_targets($amount, $target, $results)
    {
        $money = new Money($amount, new Currency(self::CURRENCY));

        $allocated = $money->allocateTo($target);

        foreach ($allocated as $key => $money) {
            $compareTo = new Money($results[$key], $money->getCurrency());

            $this->assertTrue($money->equals($compareTo));
        }
    }

    /**
     * @dataProvider comparatorExamples
     * @test
     */
    public function it_has_comparators($amount, $isZero, $isPositive, $isNegative)
    {
        $money = new Money($amount, new Currency(self::CURRENCY));

        $this->assertEquals($isZero, $money->isZero());
        $this->assertEquals($isPositive, $money->isPositive());
        $this->assertEquals($isNegative, $money->isNegative());
    }

    /**
     * @dataProvider absoluteExamples
     * @test
     */
    public function it_calculates_the_absolute_amount($amount, $result)
    {
        $money = new Money($amount, new Currency(self::CURRENCY));

        $money = $money->absolute();

        $this->assertEquals($result, $money->getAmount());
    }

    public function test_it_converts_to_json()
    {
        $this->assertEquals(
            '{"amount":"350","currency":"EUR"}',
            json_encode(Money::EUR(350))
        );
    }

    public function test_it_supports_max_int()
    {
        $one = new Money(1, new Currency('EUR'));

        $this->assertInstanceOf(Money::class, new Money(PHP_INT_MAX, new Currency('EUR')));
        $this->assertInstanceOf(Money::class, (new Money(PHP_INT_MAX, new Currency('EUR')))->add($one));
        $this->assertInstanceOf(Money::class, (new Money(PHP_INT_MAX, new Currency('EUR')))->subtract($one));
    }

    public function equalityExamples()
    {
        return [
            [self::AMOUNT, new Currency(self::CURRENCY), true],
            [self::AMOUNT + 1, new Currency(self::CURRENCY), false],
            [self::AMOUNT, new Currency(self::OTHER_CURRENCY), false],
            [self::AMOUNT + 1, new Currency(self::OTHER_CURRENCY), false],
        ];
    }

    public function comparisonExamples()
    {
        return [
            [self::AMOUNT, 0],
            [self::AMOUNT - 1, 1],
            [self::AMOUNT + 1, -1],
        ];
    }

    public function invalidOperandExamples()
    {
        return [
            [[]],
            [false],
            ['operand'],
            [null],
            [new \stdClass()],
        ];
    }

    public function allocationExamples()
    {
        return [
            [100, [1, 1, 1], [34, 33, 33]],
            [101, [1, 1, 1], [34, 34, 33]],
            [5, [3, 7], [2, 3]],
            [5, [7, 3], [4, 1]],
            [5, [7, 3, 0], [4, 1, 0]],
            [-5, [7, 3], [-3, -2]],
        ];
    }

    public function allocationTargetExamples()
    {
        return [
            [15, 2, [8, 7]],
            [10, 2, [5, 5]],
            [15, 3, [5, 5, 5]],
            [10, 3, [4, 3, 3]],
        ];
    }

    public function comparatorExamples()
    {
        return [
            [1, false, true, false],
            [0, true, false, false],
            [-1, false, false, true],
            ['1', false, true, false],
            ['0', true, false, false],
            ['-1', false, false, true],
        ];
    }

    public function absoluteExamples()
    {
        return [
            [1, 1],
            [0, 0],
            [-1, 1],
            ['1', 1],
            ['0', 0],
            ['-1', 1],
        ];
    }
}
