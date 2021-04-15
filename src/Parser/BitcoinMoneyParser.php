<?php

declare(strict_types=1);

namespace Money\Parser;

use Money\Currencies\BitcoinCurrencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;

use function is_string;
use function ltrim;
use function rtrim;
use function str_pad;
use function str_replace;
use function strlen;
use function strpos;
use function substr;
use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * Parses Bitcoin currency to Money.
 */
final class BitcoinMoneyParser implements MoneyParser
{
    private int $fractionDigits;

    public function __construct(int $fractionDigits)
    {
        $this->fractionDigits = $fractionDigits;
    }

    public function parse(string $money, Currency|null $forceCurrency = null): Money
    {
        if (is_string($money) === false) {
            throw new ParserException('Formatted raw money should be string, e.g. $1.00');
        }

        if (strpos($money, BitcoinCurrencies::SYMBOL) === false) {
            throw new ParserException('Value cannot be parsed as Bitcoin');
        }

        if ($forceCurrency === null) {
            $forceCurrency = new Currency(BitcoinCurrencies::CODE);
        }

        /*
         * This conversion is only required whilst currency can be either a string or a
         * Currency object.
         */
        $currency = $forceCurrency;
        if (! $currency instanceof Currency) {
            @trigger_error('Passing a currency as string is deprecated since 3.1 and will be removed in 4.0. Please pass a ' . Currency::class . ' instance instead.', E_USER_DEPRECATED);
            $currency = new Currency($currency);
        }

        $decimal          = str_replace(BitcoinCurrencies::SYMBOL, '', $money);
        $decimalSeparator = strpos($decimal, '.');

        if ($decimalSeparator !== false) {
            $decimal       = rtrim($decimal, '0');
            $lengthDecimal = strlen($decimal);
            $decimal       = str_replace('.', '', $decimal);
            $decimal      .= str_pad('', ($lengthDecimal - $decimalSeparator - $this->fractionDigits - 1) * -1, '0');
        } else {
            $decimal .= str_pad('', $this->fractionDigits, '0');
        }

        if (substr($decimal, 0, 1) === '-') {
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
