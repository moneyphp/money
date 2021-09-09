<?php

declare(strict_types=1);

namespace spec\Money\Currencies;

use Money\Currencies;
use Money\Currencies\CurrencyList;
use Money\Currency;
use PhpSpec\ObjectBehavior;

final class CurrencyListSpec extends ObjectBehavior
{
    use Matchers;

    public function let(): void
    {
        $this->beConstructedWith([
            'MY1' => 2,
            'MY2' => 0,
            'MY3' => 1,
        ]);
    }

    public function it_is_a_currency_repository(): void
    {
        $this->shouldImplement(Currencies::class);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CurrencyList::class);
    }

    public function it_contains_custom_currency(): void
    {
        $this->contains(new Currency('MY1'))->shouldReturn(true);
    }

    public function it_does_not_contain_currency(): void
    {
        $this->contains(new Currency('EUR'))->shouldReturn(false);
    }

    public function it_is_iterable(): void
    {
        $this->getIterator()->shouldHaveCurrency('MY1');
    }
}
