<?php

namespace Tests\MoneyStaticAnalysis;

use Money\Currency;
use Money\Money;

/** @psalm-pure */
function consumeMoney(Money $money): Money
{
    return Money::avg(
        new Money(100, new Currency('USD')),
        Money::max(
            new Money(1, new Currency('USD')),
            Money::min(
                new Money(10000, new Currency('USD')),
                Money::sum(new Money(123, new Currency('USD')), $money)
                    ->subtract(new Money(456, new Currency('USD')))
            )
        )
    );
}
