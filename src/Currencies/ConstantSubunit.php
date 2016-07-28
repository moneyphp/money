<?php

namespace Money\Currencies;

use Money\CurrenciesWithSubunit;
use Money\Currency;

/**
 * Provide a constant subunit for every currency.
 *
 * @author Frederik Bosch
 */
final class ConstantSubunit implements CurrenciesWithSubunit
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
    public function getSubunitFor(Currency $currency)
    {
        return $this->subunit;
    }
}
