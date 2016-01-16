<?php

namespace Tests\Money\Exception;

use Money\Currency;
use Money\Exception\UnresolvableCurrencyPairException;

/**
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class UnresolvableCurrencyPairExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testStaticFactory()
    {
        $baseCurrency = new Currency('EUR');
        $counterCurrency = new Currency('NOPE');

        $e = UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);

        $this->assertEquals(
            'Cannot resolve a currency pair for currencies: EUR/NOPE',
            $e->getMessage()
        );
    }
}
