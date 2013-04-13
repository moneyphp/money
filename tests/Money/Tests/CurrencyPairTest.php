<?php
/**
 * This file is part of the Money library
 *
 * Copyright (c) 2011-2013 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money\Tests;

use PHPUnit_Framework_TestCase;
use Money\Money;
use Money\Currency;
use Money\CurrencyPair;

class CurrencyPairTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function ConvertsEurToUsdAndBack()
    {
        $eur = Money::EUR(100);

        $pair = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);
        $usd = $pair->convert($eur);
        $this->assertEquals(Money::USD(125), $usd);

        $pair = new CurrencyPair(new Currency('USD'), new Currency('EUR'), 0.8000);
        $eur = $pair->convert($usd);
        $this->assertEquals(Money::EUR(100), $eur);
    }

    /** @test */
    public function ParsesIso()
    {
        $pair = CurrencyPair::createFromIso('EUR/USD 1.2500');
        $expected = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);
        $this->assertEquals($expected, $pair);
    }

    /** @test */
    public function CurrencyPairThrowsExceptionOnRatioNonNumeric()
    {
        $this->setExpectedException(
            'Money\InvalidArgumentException',
            'Ratio must be numeric'
        );

        $ratio = 'foo';

        $pair = new CurrencyPair(new Currency('EUR'), new Currency('USD'), $ratio);
    }

    /** @test */
    public function CreateFromIsoThrowsExceptionOnIsoNotMatchCurrencyPattern()
    {
        $this->setExpectedException(
            'Money\InvalidArgumentException',
            'ISO does not match accepted currency patterns'
        );

        $pair = CurrencyPair::createFromIso('foo/bam 1.2500');
    }

    /** @test */
    public function ConvertThrowsExceptionOnWrongCurrencyType()
    {
        $this->setExpectedException(
            'Money\InvalidArgumentException',
            'The Money has the wrong currency'
        );

        $eur = Money::EUR(100);
        $currentPair = new CurrencyPair(new Currency('USD'), new Currency('EUR'), 1.2500);

        $currentPair->convert($eur);
    }
}
