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
     * @test
     */
    public function it_multiplies_the_amount_with_locale_that_uses_comma_separator()
    {
        setlocale(LC_ALL, 'es_ES.utf8');

        $money = new Money(100, new Currency(self::CURRENCY));
        $money = $money->multiply(10 / 100);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals(10, $money->getAmount());

        setlocale(LC_ALL, null);
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

    /**
     * @dataProvider negativeExamples
     * @test
     */
    public function it_calculates_the_negative_amount($amount, $result)
    {
        $money = new Money($amount, new Currency(self::CURRENCY));

        $money = $money->negative();

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
            [(string) self::AMOUNT, new Currency(self::CURRENCY), true],
            [((string) self::AMOUNT).'.000', new Currency(self::CURRENCY), true],
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

    public function negativeExamples()
    {
        return [
            [1, -1],
            [0, 0],
            [-1, 1],
            ['1', -1],
            ['0', 0],
            ['-1', 1],
        ];
    }

    public function multiplyOtherExamples()
    {
        return [
            [0, 1, 0],
            [0, 7, 0],
            [5, 36, 180],
            [8, 2, 16],
            [12, 7, 84],
            [15, 11, 165],
            [16, 21, 336],
            [18, 31, 558],
            [22, 139, 3058],
            [24, 22, 528],
            [26, 1, 26],
            [29, 22, 638],
            [29, 19, 551],
            [31, 106, 3286],
            [33, 87, 2871],
            [37, 116, 4292],
            [40, 79, 3160],
            [41, 3, 123],
            [41, 30, 1230],
            [42, 111, 4662],
            [43, 37, 1591],
            [48, 58, 2784],
            [48, 13, 624],
            [57, 104, 5928],
            [58, 43, 2494],
            [68, 6, 408],
            [68, 29, 1972],
            [78, 41, 3198],
            [81, 138, 11178],
            [87, 45, 3915],
            [106, 71, 7526],
            [109, 45, 4905],
            [122, 66, 8052],
            [129, 47, 6063],
            [131, 198, 25938],
            [142, 145, 20590],
            [158, 59, 9322],
            [159, 159, 25281],
            [191, 196, 37436],
        ];
    }

    public function divideOtherExamples()
    {
        return [
            [0, 1, Money::ROUND_UP, 0],
            [0, 1, Money::ROUND_DOWN, 0],
            [1, 43, Money::ROUND_DOWN, 0],
            [14, 9, Money::ROUND_UP, 2],
            [17, 2, Money::ROUND_DOWN, 8],
            [28, 8, Money::ROUND_DOWN, 3],
            [40, 2, Money::ROUND_UP, 20],
            [136, 6, Money::ROUND_UP, 23],
            [160, 9, Money::ROUND_UP, 18],
            [175, 8, Money::ROUND_DOWN, 21],
            [180, 19, Money::ROUND_UP, 10],
            [333, 3, Money::ROUND_UP, 111],
            [398, 58, Money::ROUND_DOWN, 6],
            [407, 5, Money::ROUND_DOWN, 81],
            [732, 3, Money::ROUND_DOWN, 244],
            [798, 55, Money::ROUND_DOWN, 14],
            [840, 24, Money::ROUND_UP, 35],
            [898, 5, Money::ROUND_DOWN, 179],
            [943, 26, Money::ROUND_UP, 37],
            [947, 32, Money::ROUND_DOWN, 29],
            [1173, 5, Money::ROUND_UP, 235],
            [1298, 3, Money::ROUND_DOWN, 432],
            [1379, 17, Money::ROUND_UP, 82],
            [1389, 16, Money::ROUND_UP, 87],
            [1509, 75, Money::ROUND_DOWN, 20],
            [1793, 63, Money::ROUND_UP, 29],
            [1798, 103, Money::ROUND_DOWN, 17],
            [2296, 41, Money::ROUND_DOWN, 56],
            [2734, 73, Money::ROUND_UP, 38],
            [3168, 53, Money::ROUND_UP, 60],
            [3329, 55, Money::ROUND_UP, 61],
            [3359, 40, Money::ROUND_DOWN, 83],
            [3603, 14, Money::ROUND_DOWN, 257],
            [3708, 7, Money::ROUND_UP, 530],
            [3901, 10, Money::ROUND_UP, 391],
            [3916, 74, Money::ROUND_DOWN, 52],
            [4011, 181, Money::ROUND_DOWN, 22],
            [4047, 15, Money::ROUND_DOWN, 269],
            [4082, 42, Money::ROUND_UP, 98],
            [4307, 56, Money::ROUND_DOWN, 76],
            [4754, 17, Money::ROUND_UP, 280],
            [4842, 59, Money::ROUND_DOWN, 82],
            [5848, 83, Money::ROUND_UP, 71],
            [6149, 15, Money::ROUND_DOWN, 409],
            [6588, 17, Money::ROUND_DOWN, 387],
            [6696, 77, Money::ROUND_DOWN, 86],
            [7154, 95, Money::ROUND_UP, 76],
            [7391, 49, Money::ROUND_DOWN, 150],
            [7580, 155, Money::ROUND_UP, 49],
            [8141, 60, Money::ROUND_UP, 136],
            [8451, 53, Money::ROUND_UP, 160],
            [9361, 50, Money::ROUND_DOWN, 187],
            [9520, 81, Money::ROUND_DOWN, 117],
            [12777, 140, Money::ROUND_UP, 92],
            [13135, 10, Money::ROUND_UP, 1314],
            [13289, 47, Money::ROUND_DOWN, 282],
            [14936, 102, Money::ROUND_UP, 147],
            [15185, 186, Money::ROUND_DOWN, 81],
            [15612, 126, Money::ROUND_UP, 124],
            [15700, 119, Money::ROUND_UP, 132],
            [17102, 121, Money::ROUND_DOWN, 141],
            [17258, 54, Money::ROUND_DOWN, 319],
            [17562, 158, Money::ROUND_DOWN, 111],
            [18252, 57, Money::ROUND_UP, 321],
            [18941, 39, Money::ROUND_UP, 486],
            [19662, 80, Money::ROUND_DOWN, 245],
            [20048, 141, Money::ROUND_UP, 143],
            [21341, 16, Money::ROUND_DOWN, 1333],
            [21564, 131, Money::ROUND_UP, 165],
            [22346, 117, Money::ROUND_UP, 191],
            [23720, 126, Money::ROUND_UP, 189],
            [26536, 210, Money::ROUND_DOWN, 126],
            [27823, 182, Money::ROUND_UP, 153],
            [28727, 14, Money::ROUND_DOWN, 2051],
            [29279, 17, Money::ROUND_UP, 1723],
            [29856, 207, Money::ROUND_UP, 145],
            [29893, 170, Money::ROUND_DOWN, 175],
            [34119, 19, Money::ROUND_UP, 1796],
            [34586, 63, Money::ROUND_DOWN, 548],
            [34682, 52, Money::ROUND_DOWN, 666],
        ];
    }
    
    /**
     * @dataProvider multiplyOtherExamples
     * @test
     */
    public function it_multiplies_other_money($amount, $other, $result)
    {
        $money = new Money($amount, new Currency(self::CURRENCY));
        $other = new Money($other, new Currency(self::CURRENCY));

        $money = $money->multiply($other, Money::ROUND_HALF_UP);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals((string) $result, $money->getAmount());
    }

    /**
     * @dataProvider divideOtherExamples
     * @test
     */
    public function it_divides_other_money($amount, $other, $roundingMode, $result)
    {
        $money = new Money($amount, new Currency(self::CURRENCY));
        $oher = new Money($other, new Currency(self::CURRENCY));

        $money = $money->divide($oher, $roundingMode);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals((string) $result, $money->getAmount());
    }

}
