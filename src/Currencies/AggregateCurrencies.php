<?php

declare(strict_types=1);

namespace Money\Currencies;

use AppendIterator;
use IteratorIterator;
use Money\Currencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use Traversable;

/**
 * Aggregates several currency repositories.
 */
final class AggregateCurrencies implements Currencies
{
    /**
     * @param Currencies[] $currencies
     */
    public function __construct(private readonly array $currencies)
    {
    }

    public function contains(Currency $currency): bool
    {
        foreach ($this->currencies as $currencies) {
            if ($currencies->contains($currency)) {
                return true;
            }
        }

        return false;
    }

    public function subunitFor(Currency $currency): int
    {
        foreach ($this->currencies as $currencies) {
            if ($currencies->contains($currency)) {
                return $currencies->subunitFor($currency);
            }
        }

        throw new UnknownCurrencyException('Cannot find currency ' . $currency->getCode());
    }

    /** {@inheritDoc} */
    public function getIterator(): Traversable
    {
        $iterator = new AppendIterator();

        foreach ($this->currencies as $currencies) {
            $iterator->append(new IteratorIterator($currencies->getIterator()));
        }

        return $iterator;
    }
}
