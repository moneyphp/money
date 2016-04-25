<?php

namespace Money\Parser;

use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;

/**
 * Parses a string into a Money object regular expressions.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class StringToUnitsParser implements MoneyParser
{
    /**
     * Creates units from string and returns Money.
     *
     * {@inheritdoc}
     */
    public function parse($money, $forceCurrency = null)
    {
        if (!is_string($money)) {
            throw new ParserException('Formatted raw money should be string, e.g. $1.00');
        }

        if ($forceCurrency === null) {
            throw new ParserException(
                'StringToUnitsParser cannot parse currency symbols. Use forceCurrency argument'
            );
        }

        $sign = "(?P<sign>[-\+])?";
        $digits = "(?P<digits>\d*)";
        $separator = '(?P<separator>[.,])?';
        $decimals = "(?P<decimal1>\d)?(?P<decimal2>\d)?";
        $pattern = '/^'.$sign.$digits.$separator.$decimals.'$/';

        if (!preg_match($pattern, trim($money), $matches)) {
            throw new ParserException('The value could not be parsed as money');
        }

        $units = $matches['sign'] === '-' ? '-' : '';
        $units .= $matches['digits'];
        $units .= isset($matches['decimal1']) ? $matches['decimal1'] : '0';
        $units .= isset($matches['decimal2']) ? $matches['decimal2'] : '0';

        if ($matches['sign'] === '-') {
            $units = '-'.ltrim(substr($units, 1), '0');
        } else {
            $units = ltrim($units, '0');
        }

        if ($units === '' || $units === '-') {
            $units = '0';
        }

        return new Money($units, new Currency($forceCurrency));
    }
}
