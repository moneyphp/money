<?php

namespace spec\Money;

use Money\Currency;
use Money\CurrencyPair;
use Money\Money;
use PhpSpec\ObjectBehavior;

class CurrencyPairSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new Currency('EUR'), new Currency('USD'), 1.250000);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\CurrencyPair');
    }

    function it_has_currencies_and_ratio()
    {
        $this->beConstructedWith($base = new Currency('EUR'), $counter = new Currency('USD'), $ratio = 1.0);

        $this->getBaseCurrency()->shouldReturn($base);
        $this->getCounterCurrency()->shouldReturn($counter);
        $this->getConversionRatio()->shouldReturn($ratio);
    }

    function it_throws_an_exception_when_ratio_is_not_numeric()
    {
        $this->beConstructedWith(new Currency('EUR'), new Currency('USD'), 'NON_NUMERIC');

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_an_exception_when_trying_to_convert_an_invalid_currency()
    {
        $money = new Money(100, new Currency('JPY'));

        $this->shouldThrow(\InvalidArgumentException::class)->duringConvert($money);
    }

    function it_converts_from_base_currency_to_counter()
    {
        $money = new Money(100, new Currency('EUR'));

        $resultMoney = $this->convert($money);

        $resultMoney->shouldHaveType(Money::class);
        $resultMoney->getAmount()->shouldBeLike(125);
        $resultMoney->getCurrency()->getCode()->shouldReturn('USD');
    }

    function it_converts_using_rounding_modes()
    {
        $money = new Money(10, new Currency('EUR'));

        $resultMoney = $this->convert($money);

        $resultMoney->shouldHaveType(Money::class);
        $resultMoney->getAmount()->shouldBeLike(13);
        $resultMoney->getCurrency()->getCode()->shouldReturn('USD');

        $resultMoney = $this->convert($money, PHP_ROUND_HALF_DOWN);

        $resultMoney->shouldHaveType(Money::class);
        $resultMoney->getAmount()->shouldBeLike(12);
        $resultMoney->getCurrency()->getCode()->shouldReturn('USD');
    }

    /**
     * @dataProvider equalityExamples
     */
    function it_equals_to_another_currency_pair($pair, $equality)
    {
        $this->equals($pair)->shouldReturn($equality);
    }

    public function equalityExamples()
    {
        return [
            [new CurrencyPair(new Currency('GBP'), new Currency('USD'), 1.250000), false],
            [new CurrencyPair(new Currency('EUR'), new Currency('GBP'), 1.250000), false],
            [new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.5000), false],
            [new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.250000), true],
        ];
    }

    function it_parses_an_iso_string()
    {
        $pair = $this->createFromIso('EUR/USD 1.250000');

        $this->equals($pair)->shouldReturn(true);
    }

    function it_throws_an_exception_when_iso_string_cannot_be_parsed()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringCreateFromIso('1.250000');
    }
}
