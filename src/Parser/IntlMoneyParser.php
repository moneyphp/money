<?php

declare(strict_types=1);

namespace Money\Parser;

use Money\Currencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;
use Money\Number;
use NumberFormatter;

use function is_string;
use function ltrim;
use function str_pad;
use function str_replace;
use function strlen;
use function strpos;
use function substr;
use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * Parses a string into a Money object using intl extension.
 */
final class IntlMoneyParser implements MoneyParser
{
    private NumberFormatter $formatter;

    private Currencies $currencies;

    public function __construct(NumberFormatter $formatter, Currencies $currencies)
    {
        $this->formatter  = $formatter;
        $this->currencies = $currencies;
    }

    public function parse(string $money, Currency|null $forceCurrency = null): Money
    {
        if (! is_string($money)) {
            throw new ParserException('Formatted raw money should be string, e.g. $1.00');
        }

        $currency = null;
        $decimal  = $this->formatter->parseCurrency($money, $currency);

        if ($decimal === false) {
            throw new ParserException('Cannot parse ' . $money . ' to Money. ' . $this->formatter->getErrorMessage());
        }

        if ($forceCurrency !== null) {
            $currency = $forceCurrency;
        } else {
            $currency = new Currency($currency);
        }

        /*
         * This conversion is only required whilst currency can be either a string or a
         * Currency object.
         */
        if (! $currency instanceof Currency) {
            trigger_error('Passing a currency as string is deprecated since 3.1 and will be removed in 4.0. Please pass a ' . Currency::class . ' instance instead.', E_USER_DEPRECATED);
            $currency = new Currency($currency);
        }

        $decimal         = (string) $decimal;
        $subunit         = $this->currencies->subunitFor($currency);
        $decimalPosition = strpos($decimal, '.');

        if ($decimalPosition !== false) {
            $decimalLength  = strlen($decimal);
            $fractionDigits = $decimalLength - $decimalPosition - 1;
            $decimal        = str_replace('.', '', $decimal);
            $decimal        = Number::roundMoneyValue($decimal, $subunit, $fractionDigits);

            if ($fractionDigits > $subunit) {
                $decimal = substr($decimal, 0, $decimalPosition + $subunit);
            } elseif ($fractionDigits < $subunit) {
                $decimal .= str_pad('', $subunit - $fractionDigits, '0');
            }
        } else {
            $decimal .= str_pad('', $subunit, '0');
        }

        if ($decimal[0] === '-') {
            $decimal = '-' . ltrim(substr($decimal, 1), '0');
        } else {
            $decimal = ltrim($decimal, '0');
        }

        if ($decimal === '') {
            $decimal = '0';
        }

        return new Money($decimal, $currency);
    }
}
