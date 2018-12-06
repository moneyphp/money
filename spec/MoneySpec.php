<?php

namespace spec\Money;

use Money\Calculator;
use Money\Currency;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class MoneySpec extends ObjectBehavior
{
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
        $this->shouldHaveType(Money::class);
    }

    function it_is_json_serializable()
    {
        $this->shouldImplement(\JsonSerializable::class);
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

    function it_throws_an_exception_when_amount_is_not_numeric()
    {
        $this->beConstructedWith('ONE', new Currency(self::CURRENCY));

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_constructs_integer()
    {
        $this->beConstructedWith(5, new Currency(self::CURRENCY));
    }

    function it_constructs_string()
    {
        $this->beConstructedWith('5', new Currency(self::CURRENCY));
    }

    function it_constructs_integer_with_decimals_of_zero()
    {
        $this->beConstructedWith('5.00', new Currency(self::CURRENCY));
    }

    function it_constructs_integer_with_plus()
    {
        $this->beConstructedWith('+500', new Currency(self::CURRENCY));

        $this->shouldNotThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_tests_currency_equality()
    {
        $this->isSameCurrency(new Money(self::AMOUNT, new Currency(self::CURRENCY)))->shouldReturn(true);
        $this->isSameCurrency(new Money(self::AMOUNT, new Currency(self::OTHER_CURRENCY)))->shouldReturn(false);
    }

    function it_equals_to_another_money()
    {
        $this->equals(new Money(self::AMOUNT, new Currency(self::CURRENCY)))->shouldReturn(true);
    }

    function it_compares_two_amounts(Calculator $calculator)
    {
        $calculator->compare((string) self::AMOUNT, (string) self::AMOUNT)->willReturn(0);
        $money = new Money(self::AMOUNT, new Currency(self::CURRENCY));

        $this->compare($money)->shouldReturn(0);
        $this->greaterThan($money)->shouldReturn(false);
        $this->greaterThanOrEqual($money)->shouldReturn(true);
        $this->lessThan($money)->shouldReturn(false);
        $this->lessThanOrEqual($money)->shouldReturn(true);
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
        $money->getAmount()->shouldBe((string) $result);
    }

    function it_adds_other_money_values(Calculator $calculator)
    {
        $result = self::AMOUNT + self::OTHER_AMOUNT + self::AMOUNT + self::OTHER_AMOUNT;

        $calculator->add((string) self::AMOUNT, (string) self::OTHER_AMOUNT)->willReturn((string) (self::AMOUNT + self::OTHER_AMOUNT));
        $calculator->add((string) (self::AMOUNT + self::OTHER_AMOUNT), (string) self::AMOUNT)->willReturn((string) (self::AMOUNT + self::OTHER_AMOUNT + self::AMOUNT));
        $calculator->add((string) (self::AMOUNT + self::OTHER_AMOUNT + self::AMOUNT), (string) self::OTHER_AMOUNT)->willReturn((string) $result);
        $money = $this->add(
            new Money(self::OTHER_AMOUNT, new Currency(self::CURRENCY)),
            new Money(self::AMOUNT, new Currency(self::CURRENCY)),
            new Money(self::OTHER_AMOUNT, new Currency(self::CURRENCY))
        );

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBe((string) $result);
    }

    function it_returns_the_same_money_when_no_addends_are_provided()
    {
        $money = $this->add();

        $money->getAmount()->shouldBe($this->getAmount());
    }

    function it_throws_an_exception_when_currency_is_different_during_addition(Calculator $calculator)
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
        $money->getAmount()->shouldBe((string) $result);
    }

    function it_subtracts_other_money_values(Calculator $calculator)
    {
        $this->beConstructedWith(self::AMOUNT + self::OTHER_AMOUNT + self::AMOUNT + self::OTHER_AMOUNT, new Currency(self::CURRENCY));

        $calculator->subtract((string) (self::AMOUNT + self::OTHER_AMOUNT + self::AMOUNT + self::OTHER_AMOUNT), (string) self::OTHER_AMOUNT)->willReturn((string) (self::AMOUNT + self::OTHER_AMOUNT + self::AMOUNT));
        $calculator->subtract((string) (self::AMOUNT + self::OTHER_AMOUNT + self::AMOUNT), (string) self::AMOUNT)->willReturn((string) (self::AMOUNT + self::OTHER_AMOUNT));
        $calculator->subtract((string) (self::AMOUNT + self::OTHER_AMOUNT), (string) self::OTHER_AMOUNT)->willReturn((string) self::AMOUNT);
        $money = $this->subtract(
            new Money(self::OTHER_AMOUNT, new Currency(self::CURRENCY)),
            new Money(self::AMOUNT, new Currency(self::CURRENCY)),
            new Money(self::OTHER_AMOUNT, new Currency(self::CURRENCY))
        );

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBe((string) self::AMOUNT);
    }

    function it_returns_the_same_money_when_no_subtrahends_are_provided()
    {
        $money = $this->subtract();

        $money->getAmount()->shouldBe($this->getAmount());
    }

    function it_throws_an_exception_if_currency_is_different_during_subtractition(Calculator $calculator)
    {
        $calculator->subtract((string) self::AMOUNT, (string) self::AMOUNT)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->duringSubtract(new Money(self::AMOUNT, new Currency(self::OTHER_CURRENCY)));
    }

    function it_multiplies_the_amount(Calculator $calculator)
    {
        $this->beConstructedWith(1, new Currency(self::CURRENCY));

        $calculator->multiply('1', 5)->willReturn(5);
        $calculator->round(5, Money::ROUND_HALF_UP)->willReturn(5);

        $money = $this->multiply(5);

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBe('5');
    }

    public function it_throws_an_exception_when_operand_is_invalid_during_multiplication(Calculator $calculator)
    {
        $calculator->multiply(Argument::type('string'), Argument::type('numeric'))->shouldNotBeCalled();
        $calculator->round(Argument::type('string'), Argument::type('integer'))->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->duringMultiply('INVALID_OPERAND');
    }

    public function it_throws_an_exception_when_rounding_mode_is_invalid_during_multiplication(Calculator $calculator)
    {
        $calculator->multiply(Argument::type('string'), Argument::type('numeric'))->shouldNotBeCalled();
        $calculator->round(Argument::type('string'), Argument::type('integer'))->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->duringMultiply(1.0, 'INVALID_ROUNDING_MODE');
    }

    function it_divides_the_amount(Calculator $calculator)
    {
        $this->beConstructedWith(4, new Currency(self::CURRENCY));

        $calculator->compare((string) (1 / 2), '0')->willReturn(1 / 2 > 1);
        $calculator->divide('4', 1 / 2)->willReturn(2);
        $calculator->round(2, Money::ROUND_HALF_UP)->willReturn(2);

        $money = $this->divide(1 / 2, Money::ROUND_HALF_UP);

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBeLike(2);
    }

    public function it_throws_an_exception_when_operand_is_invalid_during_division(Calculator $calculator)
    {
        $calculator->compare(Argument::type('string'), Argument::type('string'))->shouldNotBeCalled();
        $calculator->divide(Argument::type('string'), Argument::type('numeric'))->shouldNotBeCalled();
        $calculator->round(Argument::type('string'), Argument::type('integer'))->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->duringDivide('INVALID_OPERAND');
    }

    public function it_throws_an_exception_when_rounding_mode_is_invalid_during_division(Calculator $calculator)
    {
        $calculator->compare('1.0', '0')->shouldNotBeCalled();
        $calculator->divide(Argument::type('string'), Argument::type('numeric'))->shouldNotBeCalled();
        $calculator->round(Argument::type('string'), Argument::type('integer'))->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->duringDivide(1.0, 'INVALID_ROUNDING_MODE');
    }

    function it_throws_an_exception_when_divisor_is_zero(Calculator $calculator)
    {
        $calculator->compare(0, '0')->willThrow(\InvalidArgumentException::class);
        $calculator->divide(Argument::type('string'), Argument::type('numeric'))->shouldNotBeCalled();
        $calculator->round(Argument::type('string'), Argument::type('integer'))->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->duringDivide(0);
    }

    function it_allocates_amount(Calculator $calculator)
    {
        $this->beConstructedWith(100, new Currency(self::CURRENCY));

        $calculator->share(Argument::type('numeric'), Argument::type('int'), Argument::type('int'))->will(function ($args) {
            return (int) floor($args[0] * $args[1] / $args[2]);
        });

        $calculator->subtract(Argument::type('numeric'), Argument::type('numeric'))->will(function ($args) {
            return (string) $args[0] - $args[1];
        });

        $calculator->add(Argument::type('numeric'), Argument::type('numeric'))->will(function ($args) {
            return (string) ($args[0] + $args[1]);
        });

        $calculator->compare(Argument::type('numeric'), Argument::type('numeric'))->will(function ($args) {
            return ($args[0] < $args[1]) ? -1 : (($args[0] > $args[1]) ? 1 : 0);
        });

        $calculator->absolute(Argument::type('numeric'))->will(function ($args) {
            return ltrim($args[0], '-');
        });

        $calculator->multiply(Argument::type('numeric'), Argument::type('int'))->will(function ($args) {
            return (string) $args[0] * $args[1];
        });

        $allocated = $this->allocate([1, 1, 1]);
        $allocated->shouldBeArray();
        $allocated->shouldEqualAllocation([34, 33, 33]);
    }

    function it_allocates_amount_to_n_targets(Calculator $calculator)
    {
        $this->beConstructedWith(15, new Currency(self::CURRENCY));

        $calculator->share(Argument::type('numeric'), Argument::type('int'), Argument::type('int'))->will(function ($args) {
            return (int) floor($args[0] * $args[1] / $args[2]);
        });

        $calculator->subtract(Argument::type('numeric'), Argument::type('numeric'))->will(function ($args) {
            return (string)($args[0] - $args[1]);
        });

        $calculator->add(Argument::type('numeric'), Argument::type('numeric'))->will(function ($args) {
            return (string)($args[0] + $args[1]);
        });

        $calculator->compare(Argument::type('numeric'), Argument::type('numeric'))->will(function ($args) {
            return ($args[0] < $args[1]) ? -1 : (($args[0] > $args[1]) ? 1 : 0);
        });

        $allocated = $this->allocateTo(2);
        $allocated->shouldBeArray();

        $allocated->shouldEqualAllocation([8, 7]);
    }

    function it_throws_an_exception_when_allocation_target_is_not_integer()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringAllocateTo('two');
    }

    function it_throws_an_exception_when_allocation_target_is_empty()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringAllocate([]);
    }

    function it_throws_an_exception_when_allocation_ratio_is_negative()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringAllocate([-1]);
    }

    function it_throws_an_exception_when_allocation_total_is_zero()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringAllocate([0, 0]);
    }

    function it_throws_an_exception_when_allocate_to_target_is_less_than_or_equals_zero()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringAllocateTo(-1);
    }

    function it_has_comparators(Calculator $calculator)
    {
        $this->beConstructedWith(1, new Currency(self::CURRENCY));

        $calculator->compare(Argument::type('numeric'), Argument::type('int'))->will(function ($args) {
            return ($args[0] < $args[1]) ? -1 : (($args[0] > $args[1]) ? 1 : 0);
        });

        $this->isZero()->shouldReturn(false);
        $this->isPositive()->shouldReturn(true);
        $this->isNegative()->shouldReturn(false);
    }

    function it_calculates_the_absolute_amount(Calculator $calculator)
    {
        $this->beConstructedWith(-1, new Currency(self::CURRENCY));

        $calculator->absolute(-1)->willReturn(1);

        $money = $this->absolute();

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBeLike(1);
    }

    function it_calculates_a_modulus_with_an_other_money(Calculator $calculator)
    {
        $result = self::AMOUNT % self::OTHER_AMOUNT;
        $calculator->mod((string) self::AMOUNT, (string) self::OTHER_AMOUNT)->willReturn((string) $result);
        $money = $this->mod(new Money(self::OTHER_AMOUNT, new Currency(self::CURRENCY)));

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBe((string) $result);
    }

    function it_throws_an_exception_when_currency_is_different_during_modulus(Calculator $calculator)
    {
        $calculator->mod((string) self::AMOUNT, (string) self::AMOUNT)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->duringMod(new Money(self::AMOUNT, new Currency(self::OTHER_CURRENCY)));
    }

    public function getMatchers()
    {
        return [
            'equalAllocation' => function ($subject, $value) {
                /** @var Money $money */
                foreach ($subject as $key => $money) {
                    $compareTo = new Money($value[$key], $money->getCurrency());
                    if ($money->equals($compareTo) === false) {
                        return false;
                    }
                }

                return true;
            },
        ];
    }
}
