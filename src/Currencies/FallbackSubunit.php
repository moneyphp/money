<?php

namespace Money\Currencies;

use Money\CurrenciesWithSubunit;
use Money\Currency;

/**
 * Provide a fallback subunit when the first fails.
 *
 * @author Frederik Bosch
 */
final class FallbackSubunit implements CurrenciesWithSubunit
{
    /**
     * @var CurrenciesWithSubunit
     */
    private $currencies;
    /**
     * @var CurrenciesWithSubunit
     */
    private $fallback;

    /**
     * @param CurrenciesWithSubunit $currencies
     * @param CurrenciesWithSubunit $fallback
     */
    public function __construct(CurrenciesWithSubunit $currencies, CurrenciesWithSubunit $fallback)
    {
        $this->currencies = $currencies;
        $this->fallback = $fallback;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubunitFor(Currency $currency)
    {
        try {
            return $this->currencies->getSubunitFor($currency);
        } catch (\Exception $e) {
            return $this->fallback->getSubunitFor($currency);
        }
    }
}
