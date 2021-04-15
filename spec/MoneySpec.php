<?php

declare(strict_types=1);

namespace spec\Money;

use InvalidArgumentException;
use JsonSerializable;
use Money\Calculator;
use Money\Currency;
use Money\Money;
use PhpSpec\Exception\Example\PendingException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use ReflectionProperty;

use function assert;
use function floor;
use function ltrim;

final class MoneySpec extends ObjectBehavior
{
    public const AMOUNT         = 10;
    public const OTHER_AMOUNT   = 5;
    public const CURRENCY       = 'EUR';
    public const OTHER_CURRENCY = 'USD';

    public function let(Calculator $calculator): void
    {
        // Override the calculator for testing
        $reflection = new ReflectionProperty(Money::class, 'calculator');
        $reflection->setAccessible(true);
        $reflection->setValue(null, $calculator->getWrappedObject());

        $this->beConstructedWith(self::AMOUNT, new Currency(self::CURRENCY));
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Money::class);
    }

    public function it_is_json_serializable(): void
    {
        $this->shouldImplement(JsonSerializable::class);
    }

    public function it_has_an_amount(): void
    {
        $this->getAmount()->shouldBeLike(self::AMOUNT);
    }

    public function it_has_a_currency(): void
    {
        $currency = $this->getCurrency();

        $currency->shouldHaveType(Currency::class);
        $currency->equals(new Currency(self::CURRENCY))->shouldReturn(true);
    }

    public function it_throws_an_exception_when_amount_is_not_numeric(): void
    {
        $this->beConstructedWith('ONE', new Currency(self::CURRENCY));

        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    public function it_constructs_integer(): void
    {
        $this->beConstructedWith(5, new Currency(self::CURRENCY));
    }

    public function it_constructs_string(): void
    {
        $this->beConstructedWith('5', new Currency(self::CURRENCY));
    }

    public function it_constructs_integer_with_decimals_of_zero(): void
    {
        $this->beConstructedWith('5.00', new Currency(self::CURRENCY));
    }

    public function it_constructs_integer_with_plus(): void
    {
        $this->beConstructedWith('+500', new Currency(self::CURRENCY));

        $this->shouldNotThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    public function it_tests_currency_equality(): void
    {
        $this->isSameCurrency(new Money(self::AMOUNT, new Currency(self::CURRENCY)))->shouldReturn(true);
        $this->isSameCurrency(new Money(self::AMOUNT, new Currency(self::OTHER_CURRENCY)))->shouldReturn(false);
    }

    public function it_equals_to_another_money(): void
    {
        $this->equals(new Money(self::AMOUNT, new Currency(self::CURRENCY)))->shouldReturn(true);
    }

    public function it_compares_two_amounts(Calculator $calculator): void
    {
        $calculator->compare((string) self::AMOUNT, (string) self::AMOUNT)->willReturn(0);
        $money = new Money(self::AMOUNT, new Currency(self::CURRENCY));

        $this->compare($money)->shouldReturn(0);
        $this->greaterThan($money)->shouldReturn(false);
        $this->greaterThanOrEqual($money)->shouldReturn(true);
        $this->lessThan($money)->shouldReturn(false);
        $this->lessThanOrEqual($money)->shouldReturn(true);
    }

    public function it_throws_an_exception_when_currency_is_different_during_comparison(Calculator $calculator): void
    {
        $calculator->compare(Argument::type('string'), Argument::type('string'))->shouldNotBeCalled();

        $money = new Money(self::AMOUNT + 1, new Currency(self::OTHER_CURRENCY));

        $this->shouldThrow(InvalidArgumentException::class)->duringCompare($money);
        $this->shouldThrow(InvalidArgumentException::class)->duringGreaterThan($money);
        $this->shouldThrow(InvalidArgumentException::class)->duringGreaterThanOrEqual($money);
        $this->shouldThrow(InvalidArgumentException::class)->duringLessThan($money);
        $this->shouldThrow(InvalidArgumentException::class)->duringLessThanOrEqual($money);
    }

    public function it_adds_an_other_money(Calculator $calculator): void
    {
        $result = self::AMOUNT + self::OTHER_AMOUNT;
        $calculator->add((string) self::AMOUNT, (string) self::OTHER_AMOUNT)->willReturn((string) $result);
        $money = $this->add(new Money(self::OTHER_AMOUNT, new Currency(self::CURRENCY)));

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBe((string) $result);
    }

    public function it_adds_other_money_values(Calculator $calculator): void
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

    public function it_returns_the_same_money_when_no_addends_are_provided(): void
    {
        $money = $this->add();

        $money->getAmount()->shouldBe($this->getAmount());
    }

    public function it_throws_an_exception_when_currency_is_different_during_addition(Calculator $calculator): void
    {
        $calculator->add((string) self::AMOUNT, (string) self::AMOUNT)->shouldNotBeCalled();

        $this->shouldThrow(InvalidArgumentException::class)->duringAdd(new Money(self::AMOUNT, new Currency(self::OTHER_CURRENCY)));
    }

    public function it_subtracts_an_other_money(Calculator $calculator): void
    {
        $result = self::AMOUNT - self::OTHER_AMOUNT;

        $calculator->subtract((string) self::AMOUNT, (string) self::OTHER_AMOUNT)->willReturn((string) $result);
        $money = $this->subtract(new Money(self::OTHER_AMOUNT, new Currency(self::CURRENCY)));

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBe((string) $result);
    }

    public function it_subtracts_other_money_values(Calculator $calculator): void
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

    public function it_returns_the_same_money_when_no_subtrahends_are_provided(): void
    {
        $money = $this->subtract();

        $money->getAmount()->shouldBe($this->getAmount());
    }

    public function it_throws_an_exception_if_currency_is_different_during_subtractition(Calculator $calculator): void
    {
        $calculator->subtract((string) self::AMOUNT, (string) self::AMOUNT)->shouldNotBeCalled();

        $this->shouldThrow(InvalidArgumentException::class)->duringSubtract(new Money(self::AMOUNT, new Currency(self::OTHER_CURRENCY)));
    }

    public function it_multiplies_the_amount(Calculator $calculator): void
    {
        $this->beConstructedWith(1, new Currency(self::CURRENCY));

        $calculator->multiply('1', 5)->willReturn(5);
        $calculator->round(5, Money::ROUND_HALF_UP)->willReturn(5);

        $money = $this->multiply(5);

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBe('5');
    }

    public function it_divides_the_amount(Calculator $calculator): void
    {
        $this->beConstructedWith(4, new Currency(self::CURRENCY));

        $calculator->compare('0.5', '0')->willReturn(1);
        $calculator->divide('4', '0.5')->willReturn('2');
        $calculator->round('2', Money::ROUND_HALF_UP)->willReturn('2');

        $money = $this->divide('0.5', Money::ROUND_HALF_UP);

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBeLike(2);
    }

    public function it_throws_an_exception_when_divisor_is_zero(Calculator $calculator): void
    {
        $calculator->compare('0', '0')->willThrow(InvalidArgumentException::class);
        $calculator->divide(Argument::type('string'), Argument::type('numeric'))->shouldNotBeCalled();
        $calculator->round(Argument::type('string'), Argument::type('integer'))->shouldNotBeCalled();

        $this->shouldThrow(InvalidArgumentException::class)->duringDivide(0);
    }

    public function it_allocates_amount(Calculator $calculator): void
    {
        $this->beConstructedWith(100, new Currency(self::CURRENCY));

        $calculator->share(Argument::type('numeric'), Argument::type('numeric'), Argument::type('numeric'))->will(function ($args) {
            return (string) floor($args[0] * $args[1] / $args[2]);
        });

        $calculator->subtract(Argument::type('numeric'), Argument::type('numeric'))->will(function ($args) {
            return (string) $args[0] - $args[1];
        });

        $calculator->add(Argument::type('numeric'), Argument::type('numeric'))->will(function ($args) {
            return (string) ($args[0] + $args[1]);
        });

        $calculator->compare(Argument::type('numeric'), Argument::type('numeric'))->will(function ($args) {
            return $args[0] < $args[1] ? -1 : ($args[0] > $args[1] ? 1 : 0);
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

    public function it_allocates_amount_to_n_targets(Calculator $calculator): void
    {
        $this->beConstructedWith(15, new Currency(self::CURRENCY));

        $calculator->share(Argument::type('numeric'), Argument::type('numeric'), Argument::type('numeric'))->will(function ($args) {
            return (string) floor($args[0] * $args[1] / $args[2]);
        });

        $calculator->subtract(Argument::type('numeric'), Argument::type('numeric'))->will(function ($args) {
            return (string) ($args[0] - $args[1]);
        });

        $calculator->add(Argument::type('numeric'), Argument::type('numeric'))->will(function ($args) {
            return (string) ($args[0] + $args[1]);
        });

        $calculator->compare(Argument::type('numeric'), Argument::type('numeric'))->will(function ($args) {
            return $args[0] < $args[1] ? -1 : ($args[0] > $args[1] ? 1 : 0);
        });

        $allocated = $this->allocateTo(2);
        $allocated->shouldBeArray();

        $allocated->shouldEqualAllocation([8, 7]);
    }

    public function it_throws_an_exception_when_allocation_target_is_empty(): void
    {
        $this->shouldThrow(InvalidArgumentException::class)->duringAllocate([]);
    }

    public function it_throws_an_exception_when_allocation_ratio_is_negative(): void
    {
        $this->shouldThrow(InvalidArgumentException::class)->duringAllocate([-1]);
    }

    public function it_throws_an_exception_when_allocation_total_is_zero(): void
    {
        $this->shouldThrow(InvalidArgumentException::class)->duringAllocate([0, 0]);
    }

    public function it_has_comparators(Calculator $calculator): void
    {
        $this->beConstructedWith(1, new Currency(self::CURRENCY));

        $calculator->compare(Argument::type('numeric'), Argument::type('numeric'))->will(function (array $args) {
            return $args[0] <=> $args[1];
        });

        $this->isZero()->shouldReturn(false);
        $this->isPositive()->shouldReturn(true);
        $this->isNegative()->shouldReturn(false);
    }

    public function it_calculates_the_absolute_amount(Calculator $calculator): void
    {
        $this->beConstructedWith(-1, new Currency(self::CURRENCY));

        $calculator->absolute(-1)->willReturn(1);

        $money = $this->absolute();

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBeLike(1);
    }

    public function it_calculates_a_modulus_with_an_other_money(Calculator $calculator): void
    {
        $result = self::AMOUNT % self::OTHER_AMOUNT;
        $calculator->mod((string) self::AMOUNT, (string) self::OTHER_AMOUNT)->willReturn((string) $result);
        $money = $this->mod(new Money(self::OTHER_AMOUNT, new Currency(self::CURRENCY)));

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBe((string) $result);
    }

    public function it_throws_an_exception_when_currency_is_different_during_modulus(Calculator $calculator): void
    {
        $calculator->mod((string) self::AMOUNT, (string) self::AMOUNT)->shouldNotBeCalled();

        $this->shouldThrow(InvalidArgumentException::class)->duringMod(new Money(self::AMOUNT, new Currency(self::OTHER_CURRENCY)));
    }

    /** {@inheritDoc} */
    public function getMatchers(): array
    {
        return [
            'equalAllocation' => function ($subject, $value) {
                foreach ($subject as $key => $money) {
                    assert($money instanceof Money);
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
