<?php

namespace Money\Currencies;

use Money\Currency;

final class ConstantSubunits implements CurrenciesWithSubunit
{
    /**
     * @var int
     */
    private $subUnits;

    /**
     * @param int $subUnits
     */
    public function __construct($subUnits)
    {
        if (!is_int($subUnits)) {
            throw new \InvalidArgumentException('Expecting integer, got '.gettype($subUnits));
        }

        $this->subUnits = $subUnits;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubunitsFor(Currency $currency)
    {
        return $this->subUnits;
    }
}
