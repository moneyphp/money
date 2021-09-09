<?php

declare(strict_types=1);

namespace spec\Money\Currencies;

use Money\Currencies;
use Money\Currencies\BitcoinCurrencies;
use Money\Currency;
use PhpSpec\ObjectBehavior;

final class BitcoinCurrenciesSpec extends ObjectBehavior
{
    use Matchers;

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(BitcoinCurrencies::class);
    }

    public function it_is_a_currency_repository(): void
    {
        $this->shouldImplement(Currencies::class);
    }

    public function it_contains_bitcoin(): void
    {
        $this->contains(new Currency('XBT'))->shouldReturn(true);
        $this->contains(new Currency('EUR'))->shouldReturn(false);
    }

    public function it_is_iterable(): void
    {
        $this->getIterator()->shouldHaveCurrency('XBT');
    }
}
