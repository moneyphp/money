<?php

namespace Tests\Money\Currencies;

use Money\Currency;
use Money\Currencies\CachedCurrencies;
use Prophecy\Argument;

final class CachedCurrenciesTest extends \PHPUnit_Framework_TestCase
{
    public function testItChecksCurrenciesInTheUnderlyingCurrencies()
    {
        $item = $this->prophesize('Psr\Cache\CacheItemInterface');
        $item->isHit()->willReturn(false);
        $item->set(true)->shouldBeCalled();
        $item->get()->willReturn(true);
        $pool = $this->prophesize('Psr\Cache\CacheItemPoolInterface');
        $pool->getItem('currency|availability|EUR')->willReturn($item);
        $pool->save($item)->shouldBeCalled();
        $currenciesMock = $this->prophesize('Money\Currencies');
        $currenciesMock->contains(Argument::type('Money\Currency'))->willReturn(true);

        $currencies = new CachedCurrencies($currenciesMock->reveal(), $pool->reveal());

        $this->assertTrue($currencies->contains(new Currency('EUR')));
    }

    public function testItChecksCurrenciesInCache()
    {
        $item = $this->prophesize('Psr\Cache\CacheItemInterface');
        $item->isHit()->willReturn(true);
        $item->set(true)->shouldNotBeCalled();
        $item->get()->willReturn(true);
        $pool = $this->prophesize('Psr\Cache\CacheItemPoolInterface');
        $pool->getItem('currency|availability|EUR')->willReturn($item);
        $pool->save($item)->shouldNotBeCalled();
        $currenciesMock = $this->prophesize('Money\Currencies');
        $currenciesMock->contains(Argument::type('Money\Currency'))->shouldNotBeCalled();

        $currencies = new CachedCurrencies($currenciesMock->reveal(), $pool->reveal());

        $this->assertTrue($currencies->contains(new Currency('EUR')));
    }
}
