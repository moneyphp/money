<?php

declare(strict_types=1);

namespace Money\Currencies;

use ArrayIterator;
use InvalidArgumentException;
use Money\Currencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use Traversable;

use function array_keys;
use function array_map;
use function is_int;
use function is_string;
use function sprintf;

/**
 * A list of custom currencies.
 */
final class CurrencyList implements Currencies
{
    /**
     * Map of currencies and their sub-units indexed by code.
     *
     * @psalm-var array<non-empty-string, int>
     */
    private array $currencies;

    /** @psalm-param array<non-empty-string, int> $currencies */
    public function __construct(array $currencies)
    {
        foreach ($currencies as $currencyCode => $subunit) {
            if (empty($currencyCode) || ! is_string($currencyCode)) {
                throw new InvalidArgumentException(sprintf('Currency code must be a string and not empty. "%s" given', $currencyCode));
            }

            if (! is_int($subunit) || $subunit < 0) {
                throw new InvalidArgumentException(sprintf('Currency %s does not have a valid minor unit. Must be a positive integer.', $currencyCode));
            }
        }

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
