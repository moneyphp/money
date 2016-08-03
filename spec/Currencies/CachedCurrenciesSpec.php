<?php

namespace spec\Money\Currencies;

use Money\Currencies;
use Money\Currency;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CachedCurrenciesSpec extends ObjectBehavior
{
    use HaveCurrencyTrait;

    function let(Currencies $currencies, CacheItemPoolInterface $pool)
    {
        $this->beConstructedWith($currencies, $pool);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Currencies\CachedCurrencies');
    }

    function it_is_a_currency_repository()
    {
        $this->shouldImplement(Currencies::class);
    }

    function it_check_currencies_using_the_delegated_ones(
        CacheItemInterface $item,
        CacheItemPoolInterface $pool,
        Currencies $currencies
    ) {
        $item->isHit()->willReturn(false);
        $item->set(true)->shouldBeCalled();
        $item->get()->willReturn(true);

        $pool->getItem('currency|availability|EUR')->willReturn($item);
        $pool->save($item)->shouldBeCalled();

        $currencies->contains(Argument::type(Currency::class))->willReturn(true);

        $this->contains(new Currency('EUR'))->shouldReturn(true);
    }

    function it_checks_currencies_from_the_cache(
        CacheItemInterface $item,
        CacheItemPoolInterface $pool,
        Currencies $currencies
    ) {
        $item->isHit()->willReturn(true);
        $item->set(true)->shouldNotBeCalled();
        $item->get()->willReturn(true);

        $pool->getItem('currency|availability|EUR')->willReturn($item);
        $pool->save($item)->shouldNotBeCalled();

        $currencies->contains(Argument::type(Currency::class))->shouldNotBeCalled();

        $this->contains(new Currency('EUR'))->shouldReturn(true);
    }

    function it_can_be_iterated(
        CacheItemInterface $item,
        CacheItemPoolInterface $pool,
        Currencies $currencies
    ) {
        $item->set(true)->shouldBeCalled();
        $pool->save($item)->shouldBeCalled();

        $pool->getItem('currency|availability|EUR')->willReturn($item);
        $currencies->getIterator()->willReturn(new \ArrayIterator([new Currency('EUR')]));

        $this->getIterator()->shouldReturnAnInstanceOf(\Traversable::class);
        $this->getIterator()->shouldHaveCurrency('EUR');
    }
}
