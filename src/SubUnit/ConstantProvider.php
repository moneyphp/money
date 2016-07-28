<?php

namespace Money\SubUnit;

use Money\Currency;
use Money\SubUnitProvider;

final class ConstantProvider implements SubUnitProvider
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
            throw new \InvalidArgumentException('Expecting integer, got '. gettype($subUnits));
        }

        $this->subUnits = $subUnits;
    }

    /**
     * @param Currency $currency
     * @return int
     */
    public function provide(Currency $currency)
    {
        return $this->subUnits;
    }
}
