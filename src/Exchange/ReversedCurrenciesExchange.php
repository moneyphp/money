<?php

declare(strict_types=1);

namespace Money\Exchange;

use Money\Calculator;
use Money\Calculator\BcMathCalculator;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;

/**
 * Tries the reverse of the currency pair if one is not available.
 *
 * Note: adding nested ReversedCurrenciesExchange could cause a huge performance hit.
 */
final class ReversedCurrenciesExchange implements Exchange
{
    /**
     * @var Calculator
     * @psalm-var class-string<Calculator>
     */
    private static string $calculator = BcMathCalculator::class;

    private Exchange $exchange;

    public function __construct(Exchange $exchange)
    {
        $this->exchange = $exchange;
    }

    /** @psalm-param class-string<Calculator> $calculator */
    public static function registerCalculator(string $calculator): void
    {
        self::$calculator = $calculator;
    }

    public function quote(Currency $baseCurrency, Currency $counterCurrency): CurrencyPair
    {
        try {
            return $this->exchange->quote($baseCurrency, $counterCurrency);
        } catch (UnresolvableCurrencyPairException $exception) {
            try {
                $currencyPair = $this->exchange->quote($counterCurrency, $baseCurrency);

                return new CurrencyPair(
                    $baseCurrency,
                    $counterCurrency,
                    self::$calculator::divide('1', $currencyPair->getConversionRatio())
                );
            } catch (UnresolvableCurrencyPairException) {
                throw $exception;
            }
        }
    }
}
