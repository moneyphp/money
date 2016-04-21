<?php

namespace Money\Currencies;

use Money\Currencies;
use Money\Currency;

/**
 * Aggregates several currency repositories.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class AggregateCurrencies implements Currencies
{
    /**
     * @var Currencies[]
     */
    private $currencies;

    /**
     * @param Currencies[] $currencies
     */
    public function __construct($currencies)
    {
        foreach ($currencies as $c) {
            $this->addCurrencies($c);
        }
    }

    /**
     * @param Currencies $currencies
     */
    private function addCurrencies(Currencies $currencies)
    {
        $this->currencies[] = $currencies;
    }

    /**
     * {@inheritdoc}
     */
    public function contains(Currency $currency)
    {
        foreach ($this->currencies as $c) {
            if ($c->contains($currency)) {
                return true;
            }
        }

        return false;
    }
}
