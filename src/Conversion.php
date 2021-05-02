<?php

declare(strict_types=1);

namespace Money;

final class Conversion
{
    private Money $money;

    private CurrencyPair $currencyPair;

    public function __construct(Money $money, CurrencyPair $currencyPair)
    {
        $this->money        = $money;
        $this->currencyPair = $currencyPair;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }

    public function getCurrencyPair(): CurrencyPair
    {
        return $this->currencyPair;
    }
}
