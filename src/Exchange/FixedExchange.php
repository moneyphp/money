<?php

namespace Money\Exchange;

use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Money\Money;

/**
 * Provides a way to get exchange rate from a static list (array).
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class FixedExchange implements Exchange
{
    /**
     * @var array
     */
    private $list;

    /**
     * @param array $list
     */
    public function __construct(array $list)
    {
        $this->list = $list;
    }

    /**
     * {@inheritdoc}
     */
    public function quote(Currency $baseCurrency, Currency $counterCurrency)
    {
        if (isset($this->list[$baseCurrency->getCode()][$counterCurrency->getCode()])) {
            return new CurrencyPair(
                $baseCurrency,
                new Money(
                    $this->list[$baseCurrency->getCode()][$counterCurrency->getCode()],
                    $counterCurrency
                )
            );
        }

        throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
    }
}
