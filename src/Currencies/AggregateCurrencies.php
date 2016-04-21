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
    public function __construct(array $currencies)
    {
        foreach ($currencies as $c) {
            if (!$c instanceof Currencies) {
                throw new \InvalidArgumentException('All currency repositories must implement Money\Currencies');
            }
        }

        $this->currencies = $currencies;
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
