<?php

namespace Money;

class NullCurrency extends Currency
{
    public function __construct()
    {
        // empty because we override Currency#__construct()
    }

    public function getName()
    {
        return '';
    }

    public function equals(Currency $other)
    {
        return true;
    }

}