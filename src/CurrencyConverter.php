<?php

namespace Money;

/**
 * Provides a way to convert Money to Money in another Currency.
 *
 * @author Gocha Ossinkine <ossinkine@ya.ru>
 */
interface CurrencyConverter
{
    /**
     * @param Money    $money
     * @param Currency $counterCurrency
     * @param int      $roundingMode
     *
     * @return Money
     */
    public function convert(Money $money, Currency $counterCurrency, $roundingMode = Money::ROUND_HALF_UP);
}
