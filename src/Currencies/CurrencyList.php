<?php

declare(strict_types=1);

namespace Money\Currencies;

use ArrayIterator;
use Money\Currencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use Traversable;

use function array_keys;
use function array_map;

/**
 * A list of custom currencies.
 */
final class CurrencyList implements Currencies
{
    /**
     * Map of currencies and their sub-units indexed by code.
     *
     * @phpstan-var array<non-empty-string, int>
     */
    private array $currencies;

    /** @phpstan-param array<non-empty-string, non-negative-int> $currencies */
    public function __construct(array $currencies)
    {
        $this->currencies = $currencies;
    }

    public function contains(Currency $currency): bool
    {
        return isset($this->currencies[$currency->getCode()]);
    }

    public function subunitFor(Currency $currency): int
    {
        if (! $this->contains($currency)) {
            throw new UnknownCurrencyException('Cannot find currency ' . $currency->getCode());
        }

        return $this->currencies[$currency->getCode()];
    }

    /** {@inheritDoc} */
    public function getIterator(): Traversable
    {
        return new ArrayIterator(
            array_map(
                static function ($code) {
                    return new Currency($code);
                },
                array_keys($this->currencies)
            )
        );
    }
}
