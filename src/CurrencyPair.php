<?php

namespace Money;

/**
 * Currency Pair holding a base, a counter currency and a conversion ratio.
 *
 * @author Mathias Verraes
 *
 * @see http://en.wikipedia.org/wiki/Currency_pair
 */
final class CurrencyPair implements \JsonSerializable
{
    /**
     * Currency to convert from.
     *
     * @var Currency
     */
    private $baseCurrency;

    /**
     * Money $baseCurrency converts to in the counter currency.
     *
     * @var Money
     */
    private $conversionRatio;

    /**
     * @param Currency $baseCurrency
     * @param Money    $conversionRatio
     *
     * @throws \InvalidArgumentException If conversion conversionRatio is null
     */
    public function __construct(Currency $baseCurrency, Money $conversionRatio)
    {
        if (is_null($conversionRatio)) {
            throw new \InvalidArgumentException('Conversion conversionRatio must not be null');
        }

        $this->counterCurrency = $counterCurrency;
        $this->conversionRatio = $conversionRatio;
    }

    /**
     * Creates a new Currency Pair based on "EUR/USD 1.2500" form representation.
     *
     * @param string $iso String representation of the form "EUR/USD 1.2500"
     *
     * @return CurrencyPair
     *
     * @throws \InvalidArgumentException Format of $iso is invalid
     */
    public static function createFromIso($iso)
    {
        $currency = '([A-Z]{2,3})';
        $ratio = "([0-9]*\.?[0-9]+)"; // @see http://www.regular-expressions.info/floatingpoint.html
        $pattern = '#'.$currency.'/'.$currency.' '.$ratio.'#';

        $matches = [];

        if (!preg_match($pattern, $iso, $matches)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Cannot create currency pair from ISO string "%s", format of string is invalid',
                    $iso
                )
            );
        }

        return new self(new Currency($matches[1]), new Money($matches[3], $matches[2]));
    }

    /**
     * Returns the Money $baseCurrency converts to in the counter currency.
     *
     * @return Money
     */
    public function getConversionRatio()
    {
        return $this->conversionRatio;
    }

    /**
     * Returns the counter currency.
     *
     * @return Currency
     */
    public function getCounterCurrency()
    {
        return $this->conversionRatio->getCurrency();
    }

    /**
     * Returns the base currency.
     *
     * @return Currency
     */
    public function getBaseCurrency()
    {
        return $this->baseCurrency;
    }

    /**
     * Checks if an other CurrencyPair has the same parameters as this.
     *
     * @param CurrencyPair $other
     *
     * @return bool
     */
    public function equals(CurrencyPair $other)
    {
        return
            $this->baseCurrency->equals($other->baseCurrency)
            && $this->conversionRatio->equals($other->conversionRatio)
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'baseCurrency' => $this->baseCurrency,
            'counterCurrency' => $this->conversionRatio->getCurrency(),
            'ratio' => $this->conversionRatio->getAmount(),
        ];
    }
}
