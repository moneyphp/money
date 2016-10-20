<?php

namespace Money\Exchange;

use Exchanger\Contract\ExchangeRateProvider;
use Exchanger\Exception\Exception as ExchangerException;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Swap\Swap;

/**
 * Provides a way to get exchange rate from a third-party source and return a currency pair.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class SwapExchange implements Exchange
{
    /**
     * @var Swap
     */
    private $swap;

    /**
     * @param Swap|ExchangeRateProvider $exchange
     */
    public function __construct($exchange)
    {
        if (!$exchange instanceof Swap && !$exchange instanceof ExchangeRateProvider) {
            throw new \InvalidArgumentException(sprintf(
                'Exchange must either be %s or %s',
                Swap::class,
                ExchangeRateProvider::class
            ));
        }

        if ($exchange instanceof ExchangeRateProvider) {
            $exchange = new Swap($exchange);
        }

        $this->swap = $exchange;
    }

    /**
     * {@inheritdoc}
     */
    public function quote(Currency $baseCurrency, Currency $counterCurrency)
    {
        try {
            $rate = $this->swap->latest($baseCurrency->getCode().'/'.$counterCurrency->getCode());
        } catch (ExchangerException $e) {
            throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
        }

        return new CurrencyPair($baseCurrency, $counterCurrency, $rate->getValue());
    }
}
