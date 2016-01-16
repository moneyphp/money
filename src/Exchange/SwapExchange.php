<?php

namespace Money\Exchange;

use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Swap\Exception\Exception as SwapException;
use Swap\Model\CurrencyPair as SwapCurrencyPair;
use Swap\SwapInterface;

/**
 * Provides a way to get exchange rate from a third-party source and return a currency pair.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class SwapExchange implements Exchange
{
    /**
     * @var SwapInterface
     */
    private $swap;

    /**
     * @param SwapInterface $swap
     */
    public function __construct(SwapInterface $swap)
    {
        $this->swap = $swap;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyPair(Currency $baseCurrency, Currency $counterCurrency)
    {
        $swapCurrencyPair = new SwapCurrencyPair($baseCurrency->getCode(), $counterCurrency->getCode());

        try {
            $rate = $this->swap->quote($swapCurrencyPair);
        } catch (SwapException $e) {
            throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
        }

        return new CurrencyPair($baseCurrency, $counterCurrency, $rate->getValue());
    }
}
