<?php

namespace Money;

final class Converter
{
    /**
     * @var Currencies
     */
    private $currencies;

    /**
     * @param Currencies $currencies
     */
    public function __construct(Currencies $currencies)
    {
        $this->currencies = $currencies;
    }

    /**
     * @param Money        $money
     * @param CurrencyPair $currencyPair
     * @param int          $roundingMode
     *
     * @return Money
     */
    public function convert(Money $money, CurrencyPair $currencyPair, $roundingMode = Money::ROUND_HALF_UP)
    {
        if ($money->getCurrency()->getCode() !== $currencyPair->getBaseCurrency()->getCode()) {
            throw new \InvalidArgumentException('Base Currency of currency pair does not equal money currency');
        }

        $subunitBaseCurrency = $this->currencies->subunitFor($currencyPair->getBaseCurrency());
        $subunitCounterCurrency = $this->currencies->subunitFor($currencyPair->getCounterCurrency());
        $differenceInSubunit = $subunitBaseCurrency - $subunitCounterCurrency;
        $ratio = $currencyPair->getConversionRatio();

        if ($differenceInSubunit > 0) {
            $ratio = $ratio / pow(10, $differenceInSubunit);
        }

        if ($differenceInSubunit < 0) {
            $ratio = $ratio * pow(10, abs($differenceInSubunit));
        }

        $counterValue = $money->multiply($ratio, $roundingMode);

        return new Money($counterValue->getAmount(), $currencyPair->getCounterCurrency());
    }
}
