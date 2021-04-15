<?php

declare(strict_types=1);

namespace spec\Money;

use InvalidArgumentException;
use JsonSerializable;
use Money\Currency;
use Money\CurrencyPair;
use PhpSpec\ObjectBehavior;

final class CurrencyPairSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(new Currency('EUR'), new Currency('USD'), '1.250000');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CurrencyPair::class);
    }

    public function it_is_json_serializable(): void
    {
        $this->shouldImplement(JsonSerializable::class);
    }

    public function it_has_currencies_and_ratio(): void
    {
        $this->beConstructedWith($base = new Currency('EUR'), $counter = new Currency('USD'), $ratio = '1.0');

        $this->getBaseCurrency()->shouldReturn($base);
        $this->getCounterCurrency()->shouldReturn($counter);
        $this->getConversionRatio()->shouldReturn($ratio);
    }

    public function it_equals_to_another_currency_pair(): void
    {
        $this->equals(new CurrencyPair(new Currency('GBP'), new Currency('USD'), '1.250000'))->shouldReturn(false);
        $this->equals(new CurrencyPair(new Currency('EUR'), new Currency('GBP'), '1.250000'))->shouldReturn(false);
        $this->equals(new CurrencyPair(new Currency('EUR'), new Currency('USD'), '1.5000'))->shouldReturn(false);
        $this->equals(new CurrencyPair(new Currency('EUR'), new Currency('USD'), '1.250000'))->shouldReturn(true);
    }

    public function it_parses_an_iso_string(): void
    {
        $pair = $this->createFromIso('EUR/USD 1.250000');

        $this->equals($pair)->shouldReturn(true);
    }

    public function it_throws_an_exception_when_iso_string_cannot_be_parsed(): void
    {
        $this->shouldThrow(InvalidArgumentException::class)->duringCreateFromIso('1.250000');
    }
}
