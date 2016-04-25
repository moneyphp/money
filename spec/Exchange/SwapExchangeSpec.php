<?php

namespace spec\Money\Exchange;

use Money\Currency;
use Money\Exception\UnresolvableCurrencyPairException;
use Swap\Exception\Exception;
use Swap\Model\CurrencyPair;
use Swap\Model\Rate;
use Swap\SwapInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SwapExchangeSpec extends ObjectBehavior
{
    function let(SwapInterface $swap)
    {
        $this->beConstructedWith($swap);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Exchange\SwapExchange');
    }

    function it_exchanges_currencies(SwapInterface $swap)
    {
        $swap->quote(Argument::type(CurrencyPair::class))->willReturn(new Rate(1.0));

        $currencyPair = $this->getCurrencyPair($base = new Currency('EUR'), $counter = new Currency('USD'));

        $currencyPair->shouldHaveType(\Money\CurrencyPair::class);
        $currencyPair->getBaseCurrency()->shouldReturn($base);
        $currencyPair->getCounterCurrency()->shouldReturn($counter);
        $currencyPair->getConversionRatio()->shouldReturn(1.0);
    }

    function it_cannot_exchange_currencies(SwapInterface $swap)
    {
        $swap->quote(Argument::type(CurrencyPair::class))->willThrow(Exception::class);

        $this->shouldThrow(UnresolvableCurrencyPairException::class)
            ->duringGetCurrencyPair(new Currency('EUR'), $counter = new Currency('USD'));
    }
}
