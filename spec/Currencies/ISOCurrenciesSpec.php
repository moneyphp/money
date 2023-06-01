<?php

declare(strict_types=1);

namespace spec\Money\Currencies;

use Money\Currencies;
use Money\Currencies\ISOCurrencies;
use PhpSpec\ObjectBehavior;

final class ISOCurrenciesSpec extends ObjectBehavior
{
    use Matchers;

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ISOCurrencies::class);
    }

    public function it_is_a_currency_repository(): void
    {
        $this->shouldImplement(Currencies::class);
    }
}
