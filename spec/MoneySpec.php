<?php

declare(strict_types=1);

namespace spec\Money;

use InvalidArgumentException;
use JsonSerializable;
use Money\Calculator;
use Money\Currency;
use Money\Money;
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

    public function let(): void
    {
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

    public function it_tests_currency_equality_with_multiple_arguments(): void
    {
        $this->isSameCurrency(
            new Money(self::AMOUNT, new Currency(self::CURRENCY)),
            new Money(self::AMOUNT, new Currency(self::CURRENCY))
        )->shouldReturn(true);

        $this->isSameCurrency(
            new Money(self::AMOUNT, new Currency(self::CURRENCY)),
            new Money(self::AMOUNT, new Currency(self::OTHER_CURRENCY))
        )->shouldReturn(false);
    }

    public function it_equals_to_another_money(): void
    {
        $this->equals(new Money(self::AMOUNT, new Currency(self::CURRENCY)))->shouldReturn(true);
    }

    public function it_returns_the_same_money_when_no_addends_are_provided(): void
    {
        $money = $this->add();

        $money->getAmount()->shouldBe($this->getAmount());
    }

    public function it_returns_the_same_money_when_no_subtrahends_are_provided(): void
    {
        $money = $this->subtract();

        $money->getAmount()->shouldBe($this->getAmount());
    }

    public function it_throws_an_exception_when_allocation_target_is_empty(): void
    {
        $this->shouldThrow(InvalidArgumentException::class)->duringAllocate([]);
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
