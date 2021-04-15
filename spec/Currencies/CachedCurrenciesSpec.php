<?php

declare(strict_types=1);

namespace spec\Money\Currencies;

use ArrayIterator;
use Money\Currencies;
use Money\Currencies\CachedCurrencies;
use Money\Currency;
use PhpSpec\ObjectBehavior;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

final class CachedCurrenciesSpec extends ObjectBehavior
{
    use Matchers;

    public function let(Currencies $currencies, CacheItemPoolInterface $pool): void
    {
        $this->beConstructedWith($currencies, $pool);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CachedCurrencies::class);
    }

    public function it_is_a_currency_repository(): void
    {
        $this->shouldImplement(Currencies::class);
    }

    public function it_check_currencies_using_the_delegated_ones(
        CacheItemInterface $item,
        CacheItemPoolInterface $pool,
        Currencies $currencies
    ): void {
        $item->isHit()->willReturn(false);
        $item->set(true)->shouldBeCalled();
        $item->get()->willReturn(true);

        $pool->getItem('currency|availability|EUR')->willReturn($item);
        $pool->save($item)->shouldBeCalled();

        $currency = new Currency('EUR');

        $currencies->contains($currency)->willReturn(true);

        $this->contains($currency)->shouldReturn(true);
    }

    public function it_checks_currencies_from_the_cache(
        CacheItemInterface $item,
        CacheItemPoolInterface $pool,
        Currencies $currencies
    ): void {
        $item->isHit()->willReturn(true);
        $item->set(true)->shouldNotBeCalled();
        $item->get()->willReturn(true);

        $pool->getItem('currency|availability|EUR')->willReturn($item);
        $pool->save($item)->shouldNotBeCalled();

        $currency = new Currency('EUR');

        $currencies->contains($currency)->shouldNotBeCalled();

        $this->contains($currency)->shouldReturn(true);
    }

    public function it_is_iterable(
        CacheItemInterface $item,
        CacheItemPoolInterface $pool,
        Currencies $currencies
    ): void {
        $item->set(true)->shouldBeCalled();
        $pool->save($item)->shouldBeCalled();

        $pool->getItem('currency|availability|EUR')->willReturn($item);
        $currencies->getIterator()->willReturn(new ArrayIterator([new Currency('EUR')]));

        $this->getIterator()->shouldHaveCurrency('EUR');
    }
}
