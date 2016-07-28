<?php

namespace Money;

/**
 * Formats Money objects.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
interface MoneyFormatter
{
    /**
     * Formats a Money object as string.
     *
     * @param Money $money
     *
     * @return string
     *
     * Exception\FormatterException
     */
    public function format(Money $money);

    /**
     * Returns Money object amount as string of subunits.
     *
     * @param Money $money
     *
     * @return string
     *
     * Exception\FormatterException
     */
    public function subunits(Money $money);
}
