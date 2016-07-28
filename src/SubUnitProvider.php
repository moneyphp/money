<?php

namespace Money;

interface SubUnitProvider
{
    /**
     * @param Currency $currency
     *
     * @return int
     */
    public function provide(Currency $currency);
}
