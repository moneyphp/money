<?php

declare(strict_types=1);

namespace Money\Exchange;

use Money\Currency;

/** @internal for sole consumption by {@see IndirectExchange} */
final class IndirectExchangeQueuedItem
{
    public bool $discovered  = false;
    public self|null $parent = null;

    public function __construct(public Currency $currency)
    {
    }
}
