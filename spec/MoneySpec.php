<?php

namespace spec\Money;

use Money\Calculator;
use Money\Currency;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MoneySpec extends ObjectBehavior
{
    use RoundExamples;

    const AMOUNT = 10;
    const OTHER_AMOUNT = 5;
    const CURRENCY = 'EUR';
    const OTHER_CURRENCY = 'USD';

    function let(Calculator $calculator)
    {
        // Override the calculator for testing
        $reflection = new \ReflectionProperty(Money::class, 'calculator');
        $reflection->setAccessible(true);
        $reflection->setValue(null, $calculator->getWrappedObject());

        $this->beConstructedWith(self::AMOUNT, new Currency(self::CURRENCY));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Money');
    }

    function it_has_an_amount()
    {
        $this->getAmount()->shouldBeLike(self::AMOUNT);
    }

    function it_has_a_currency()
    {
        $currency = $this->getCurrency();

        $currency->shouldHaveType(Currency::class);
        $currency->equals(new Currency(self::CURRENCY))->shouldReturn(true);
    }

    function it_should_throw_an_exception_when_amount_is_not_numeric()
    {
        $this->beConstructedWith('ONE', new Currency(self::CURRENCY));

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_tests_currency_equality()
    {
        $this->isSameCurrency(new Money(self::AMOUNT, new Currency(self::CURRENCY)))->shouldReturn(true);
        $this->isSameCurrency(new Money(self::AMOUNT, new Currency(self::OTHER_CURRENCY)))->shouldReturn(false);
    }

    /**
     * @dataProvider equalityExamples
     */
    function it_equals_to_another_money($amount, $currency, $equality)
    {
        $this->equals(new Money($amount, $currency))->shouldReturn($equality);
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

    /**
     * @dataProvider comparisonExamples
     */
    function it_compares_two_amounts($other, $result, Calculator $calculator)
    {
        $calculator->compare((string) self::AMOUNT, (string) $other)->willReturn($result);
        $money = new Money($other, new Currency(self::CURRENCY));

        $this->compare($money)->shouldReturn($result);
        $this->greaterThan($money)->shouldReturn(1 === $result);
        $this->greaterThanOrEqual($money)->shouldReturn(0 <= $result);
        $this->lessThan($money)->shouldReturn(-1 === $result);
        $this->lessThanOrEqual($money)->shouldReturn(0 >= $result);
    }

    public function comparisonExamples()
    {
        return [
            [self::AMOUNT, 0],
            [self::AMOUNT - 1, 1],
            [self::AMOUNT + 1, -1],
        ];
    }

    function it_throws_an_exception_when_currency_is_different_during_comparison(Calculator $calculator)
    {
        $calculator->compare(Argument::type('string'), Argument::type('string'))->shouldNotBeCalled();
        $money = new Money(self::AMOUNT + 1, new Currency(self::OTHER_CURRENCY));

        $this->shouldThrow(\InvalidArgumentException::class)->duringCompare($money);
        $this->shouldThrow(\InvalidArgumentException::class)->duringGreaterThan($money);
        $this->shouldThrow(\InvalidArgumentException::class)->duringGreaterThanOrEqual($money);
        $this->shouldThrow(\InvalidArgumentException::class)->duringLessThan($money);
        $this->shouldThrow(\InvalidArgumentException::class)->duringLessThanOrEqual($money);
    }

    function it_adds_an_other_money(Calculator $calculator)
    {
        $result = self::AMOUNT + self::OTHER_AMOUNT;
        $calculator->add((string) self::AMOUNT, (string) self::OTHER_AMOUNT)->willReturn((string) $result);
        $money = $this->add(new Money(self::OTHER_AMOUNT, new Currency(self::CURRENCY)));

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBeLike($result);
    }

    function it_throws_an_exception_if_currency_is_different_during_addition(Calculator $calculator)
    {
        $calculator->add((string) self::AMOUNT, (string) self::AMOUNT)->shouldNotBeCalled();
        $this->shouldThrow(\InvalidArgumentException::class)->duringAdd(new Money(self::AMOUNT, new Currency(self::OTHER_CURRENCY)));
    }

    function it_subtracts_an_other_money(Calculator $calculator)
    {
        $result = self::AMOUNT - self::OTHER_AMOUNT;
        $calculator->subtract((string) self::AMOUNT, (string) self::OTHER_AMOUNT)->willReturn((string) $result);
        $money = $this->subtract(new Money(self::OTHER_AMOUNT, new Currency(self::CURRENCY)));

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBeLike($result);
    }

    function it_throws_an_exception_if_currency_is_different_during_subtractition(Calculator $calculator)
    {
        $calculator->subtract((string) self::AMOUNT, (string) self::AMOUNT)->shouldNotBeCalled();
        $this->shouldThrow(\InvalidArgumentException::class)->duringSubtract(new Money(self::AMOUNT, new Currency(self::OTHER_CURRENCY)));
    }

    /**
     * @dataProvider roundExamples
     */
    function it_multiplies_the_amount($multiplier, $roundingMode, $result, Calculator $calculator)
    {
        $this->beConstructedWith(1, new Currency(self::CURRENCY));

        $calculator->multiply('1', $multiplier)->willReturn($multiplier);
        $calculator->round($multiplier, $roundingMode)->willReturn($result);

        $money = $this->multiply($multiplier, $roundingMode);

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBeLike($result);
    }

    /**
     * @dataProvider invalidOperandExamples
     */
    public function it_throws_an_exception_when_operand_is_invalid_during_multiplication($operand, Calculator $calculator)
    {
        $calculator->multiply(Argument::type('string'), Argument::type('numeric'))->shouldNotBeCalled();
        $calculator->round(Argument::type('string'), Argument::type('integer'))->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->duringMultiply($operand);
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

    public function it_throws_an_exception_when_rounding_mode_is_invalid_during_multiplication(Calculator $calculator)
    {
        $calculator->multiply(Argument::type('string'), Argument::type('numeric'))->shouldNotBeCalled();
        $calculator->round(Argument::type('string'), Argument::type('integer'))->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->duringMultiply(1.0, 'INVALID_ROUNDING_MODE');
    }

    function it_converts_to_a_different_currency(Calculator $calculator)
    {
        $this->beConstructedWith(100, new Currency(self::CURRENCY));

        $calculator->multiply('100', 1.25)->willReturn(125);
        $calculator->round(125, Money::ROUND_HALF_UP)->willReturn(125);

        $money = $this->convert(new Currency('USD'), 1.25);

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBeLike(125);
    }

    /**
     * @dataProvider roundExamples
     */
    function it_divides_the_amount($divisor, $roundingMode, $result, Calculator $calculator)
    {
        $this->beConstructedWith(1, new Currency(self::CURRENCY));

        $calculator->compare((string) (1 / $divisor), '0')->willReturn(1 / $divisor > 1);
        $calculator->divide('1', 1 / $divisor)->willReturn($divisor);
        $calculator->round($divisor, $roundingMode)->willReturn($result);

        $money = $this->divide(1 / $divisor, $roundingMode);

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBeLike($result);
    }

    /**
     * @dataProvider invalidOperandExamples
     */
    public function it_throws_an_exception_when_operand_is_invalid_during_division($operand, Calculator $calculator)
    {
        $calculator->compare(Argument::type('string'), Argument::type('string'))->shouldNotBeCalled();
        $calculator->divide(Argument::type('string'), Argument::type('numeric'))->shouldNotBeCalled();
        $calculator->round(Argument::type('string'), Argument::type('integer'))->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->duringDivide($operand);
    }

    public function it_throws_an_exception_when_rounding_mode_is_invalid_during_division(Calculator $calculator)
    {
        $calculator->compare('1.0', '0')->shouldNotBeCalled();
        $calculator->divide(Argument::type('string'), Argument::type('numeric'))->shouldNotBeCalled();
        $calculator->round(Argument::type('string'), Argument::type('integer'))->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->duringDivide(1.0, 'INVALID_ROUNDING_MODE');
    }

    /**
     * @dataProvider zeroDivisorExamples
     */
    function it_throws_an_exception_when_divisor_is_zero($divisor, Calculator $calculator)
    {
        $calculator->compare($divisor, '0')->willThrow(\InvalidArgumentException::class);
        $calculator->divide(Argument::type('string'), Argument::type('numeric'))->shouldNotBeCalled();
        $calculator->round(Argument::type('string'), Argument::type('integer'))->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->duringDivide($divisor);
    }

    // TODO: Fix moneyphp/money#213
    public function zeroDivisorExamples()
    {
        return [
            [0],
            [0.0],
            ['0'],
            ['0.0'],
        ];
    }

    /**
     * @dataProvider allocationExamples
     */
    function it_allocates_amount($amount, $ratios, $results, Calculator $calculator)
    {
        $this->beConstructedWith($amount, new Currency(self::CURRENCY));

        $calculator->share(Argument::type('numeric'), Argument::type('int'), Argument::type('int'))->will(function($args) {
            return (int) floor($args[0] * $args[1] / $args[2]);
        });

        $calculator->subtract(Argument::type('numeric'), Argument::type('int'))->will(function($args) {
            return $args[0] - $args[1];
        });

        $calculator->add(Argument::type('numeric'), Argument::type('int'))->will(function($args) {
            return $args[0] + $args[1];
        });

        $calculator->compare(Argument::type('numeric'), Argument::type('int'))->will(function($args) {
            return ($args[0] < $args[1]) ? -1 : (($args[0] > $args[1]) ? 1 : 0);
        });

        $allocated = $this->allocate($ratios);
        $allocated->shouldBeArray();

        foreach ($allocated->getWrappedObject() as $key => $allocatedMoney) {
            $allocatedMoney->equals(new Money($results[$key], new Currency('EUR')));
        }
    }

    public function allocationExamples()
    {
        return [
            [100, [1, 1, 1], [34, 33, 33]],
            [101, [1, 1, 1], [34, 34, 33]],
            [5, [3, 7], [2, 3]],
            [5, [7, 3], [4, 1]],
        ];
    }

    /**
     * @dataProvider allocationTargetExamples
     */
    function it_allocates_amount_to_n_targets($amount, $target, $results, Calculator $calculator)
    {
        $this->beConstructedWith($amount, new Currency(self::CURRENCY));

        $calculator->share(Argument::type('numeric'), Argument::type('int'), Argument::type('int'))->will(function($args) {
            return (int) floor($args[0] * $args[1] / $args[2]);
        });

        $calculator->subtract(Argument::type('numeric'), Argument::type('int'))->will(function($args) {
            return $args[0] - $args[1];
        });

        $calculator->add(Argument::type('numeric'), Argument::type('int'))->will(function($args) {
            return $args[0] + $args[1];
        });

        $calculator->compare(Argument::type('numeric'), Argument::type('int'))->will(function($args) {
            return ($args[0] < $args[1]) ? -1 : (($args[0] > $args[1]) ? 1 : 0);
        });

        $allocated = $this->allocateTo($target);
        $allocated->shouldBeArray();

        foreach ($allocated->getWrappedObject() as $key => $allocatedMoney) {
            $allocatedMoney->equals(new Money($results[$key], new Currency('EUR')));
        }
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

    function it_throws_an_exception_when_allocation_target_is_not_integer()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringAllocateTo('two');
    }

    /**
     * @dataProvider comparatorExamples
     */
    function it_has_comparators($amount, $isZero, $isPositive, $isNegative, Calculator $calculator)
    {
        $this->beConstructedWith($amount, new Currency(self::CURRENCY));

        $calculator->compare(Argument::type('numeric'), Argument::type('int'))->will(function($args) {
            return ($args[0] < $args[1]) ? -1 : (($args[0] > $args[1]) ? 1 : 0);
        });

        $this->isZero()->shouldReturn($isZero);
        $this->isPositive()->shouldReturn($isPositive);
        $this->isNegative()->shouldReturn($isNegative);
    }

    function comparatorExamples()
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
}
