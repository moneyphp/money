<?php

namespace Money\Currencies;

use Money\Currencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;

/**
 * Cache the result of currency checking.
 *
 * @author George Mponos <gmponos@gmail.com>
 */
final class SimpleCurrencies implements Currencies
{
    /**
     * Map of known currencies indexed by code.
     *
     * @var array
     */
    private $currencies;

    public function __construct(array $currencies)
    {
        foreach ($currencies as $currencyCode => $currency) {
            if (isset($currency['minorUnit']) || !isset($currency['numericCode'])) {
                throw new \InvalidArgumentException(
                    sprintf('Currency %s does not contain minorUnit or numericCode key', $currencyCode)
                );
            }
        }

        $this->currencies = $currencies;
    }

    /**
     * {@inheritdoc}
     */
    public function contains(Currency $currency)
    {
        return isset($this->currencies[$currency->getCode()]);
    }

    /**
     * {@inheritdoc}
     */
    public function subunitFor(Currency $currency)
    {
        if (!$this->contains($currency)) {
            throw new UnknownCurrencyException('Cannot find ISO currency ' . $currency->getCode());
        }

        return $this->currencies[$currency->getCode()]['minorUnit'];
    }

    /**
     * Returns the numeric code for a currency.
     *
     * @param Currency $currency
     *
     * @return int
     *
     * @throws UnknownCurrencyException If currency is not available in the current context
     */
    public function numericCodeFor(Currency $currency)
    {
        if (!$this->contains($currency)) {
            throw new UnknownCurrencyException('Cannot find ISO currency ' . $currency->getCode());
        }

        return $this->currencies[$currency->getCode()]['numericCode'];
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator(
            array_map(
                function ($code) {
                    return new Currency($code);
                },
                array_keys($this->currencies)
            )
        );
    }
}



