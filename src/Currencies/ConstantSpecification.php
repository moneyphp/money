<?php

namespace Money\Currencies;

use Money\CurrenciesSpecification;
use Money\Currency;

/**
 * Provide a constant subunit for every currency.
 *
 * @author Frederik Bosch
 */
final class ConstantSpecification implements CurrenciesSpecification
{
    /**
     * @var int
     */
    private $subunit;

    /**
     * @param int $subunit
     */
    public function __construct($subunit)
    {
        if (!is_int($subunit)) {
            throw new \InvalidArgumentException('Expecting integer, got '.gettype($subunit));
        }

        $this->subunit = $subunit;
    }

    /**
     * {@inheritdoc}
     */
    public function specify(Currency $currency)
    {
        return new Specification($currency->getCode(), $this->subunit);
    }
}
