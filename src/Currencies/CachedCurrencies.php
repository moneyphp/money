<?php

declare(strict_types=1);

namespace Money\Currencies;

use Cache\Taggable\TaggableItemInterface;
use CallbackFilterIterator;
use Money\Currencies;
use Money\Currency;
use Psr\Cache\CacheItemPoolInterface;
use Traversable;

/**
 * Cache the result of currency checking.
 */
final class CachedCurrencies implements Currencies
{
    private Currencies $currencies;

    private CacheItemPoolInterface $pool;

    public function __construct(Currencies $currencies, CacheItemPoolInterface $pool)
    {
        $this->currencies = $currencies;
        $this->pool       = $pool;
    }

    public function contains(Currency $currency): bool
    {
        $item = $this->pool->getItem('currency|availability|' . $currency->getCode());

        if ($item->isHit() === false) {
            $item->set($this->currencies->contains($currency));

            if ($item instanceof TaggableItemInterface) {
                $item->addTag('currency.availability');
            }

            $this->pool->save($item);
        }

        return $item->get();
    }

    public function subunitFor(Currency $currency): int
    {
        $item = $this->pool->getItem('currency|subunit|' . $currency->getCode());

        if ($item->isHit() === false) {
            $item->set($this->currencies->subunitFor($currency));

            if ($item instanceof TaggableItemInterface) {
                $item->addTag('currency.subunit');
            }

            $this->pool->save($item);
        }

        return $item->get();
    }

    /** {@inheritDoc} */
    public function getIterator(): Traversable
    {
        return new CallbackFilterIterator(
            $this->currencies->getIterator(),
            function (Currency $currency) {
                $item = $this->pool->getItem('currency|availability|' . $currency->getCode());
                $item->set(true);

                if ($item instanceof TaggableItemInterface) {
                    $item->addTag('currency.availability');
                }

                $this->pool->save($item);

                return true;
            }
        );
    }
}
