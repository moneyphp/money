<?php

declare(strict_types=1);

namespace spec\Money;

use JsonSerializable;
use Money\Currency;
use PhpSpec\ObjectBehavior;

final class CurrencySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('EUR');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Currency::class);
    }

    public function it_is_json_serializable(): void
    {
        $this->shouldImplement(JsonSerializable::class);
    }

    public function it_has_a_code(): void
    {
        $this->getCode()->shouldReturn('EUR');
    }

    public function it_equals_to_a_currency_with_the_same_code(): void
    {
        $this->equals(new Currency('EUR'))->shouldReturn(true);
        $this->equals(new Currency('USD'))->shouldReturn(false);
    }
}
