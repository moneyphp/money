<?php

namespace Money\Currencies;

use Money\CurrenciesSpecification;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;

/**
 * Provide a fallback subunit when the first fails.
 *
 * @author Frederik Bosch
 */
final class FallbackSpecification implements CurrenciesSpecification
{
    /**
     * @var CurrenciesSpecification
     */
    private $currencies;
    /**
     * @var CurrenciesSpecification
     */
    private $fallback;

    /**
     * @param CurrenciesSpecification $currencies
     * @param CurrenciesSpecification $fallback
     */
    public function __construct(CurrenciesSpecification $currencies, CurrenciesSpecification $fallback)
    {
        $this->currencies = $currencies;
        $this->fallback = $fallback;
    }

    /**
     * {@inheritdoc}
     */
    public function specify(Currency $currency)
    {
        try {
            return $this->currencies->specify($currency);
        } catch (UnknownCurrencyException $e) {
            return $this->fallback->specify($currency);
        }
    }
}
