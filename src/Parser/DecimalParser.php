<?php

namespace Money\Parser;

use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\Number;

/**
 * Common decimal parsing logic extracted from DecimalMoneyParser for reuse.
 *
 * @internal
 */
trait DecimalParser
{
    /**
     * @var string
     */
    public static $decimalPattern = '/^(?P<sign>-)?(?P<digits>0|[1-9]\d*)?\.?(?P<fraction>\d+)?$/';

    /**
     * @param string   $decimal
     * @param int      $subunit
     * @param Currency $currency
     * @return Money
     */
    protected function parseDecimal($decimal, $subunit, Currency $currency)
    {
        $decimal = trim($decimal);
        if ($decimal === '') {
            return new Money(0, $currency);
        }

        if (!preg_match(self::$decimalPattern, $decimal, $matches) || !isset($matches['digits'])) {
            throw new ParserException(sprintf(
                'Cannot parse "%s" to Money.',
                $decimal
            ));
        }

        $negative = isset($matches['sign']) && $matches['sign'] === '-';

        $decimal = $matches['digits'];

        if ($negative) {
            $decimal = '-'.$decimal;
        }

        if (isset($matches['fraction'])) {
            $fractionDigits = strlen($matches['fraction']);
            $decimal .= $matches['fraction'];
            $decimal = Number::roundMoneyValue($decimal, $subunit, $fractionDigits);

            if ($fractionDigits > $subunit) {
                $decimal = substr($decimal, 0, $subunit - $fractionDigits);
            } elseif ($fractionDigits < $subunit) {
                $decimal .= str_pad('', $subunit - $fractionDigits, '0');
            }
        } else {
            $decimal .= str_pad('', $subunit, '0');
        }

        if ($negative) {
            $decimal = '-'.ltrim(substr($decimal, 1), '0');
        } else {
            $decimal = ltrim($decimal, '0');
        }

        if ($decimal === '' || $decimal === '-') {
            $decimal = '0';
        }

        return new Money($decimal, $currency);
    }
}
