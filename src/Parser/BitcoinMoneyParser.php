<?php

namespace Money\Parser;

use Money\Currencies\BitcoinCurrencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;

/**
 * Parses Bitcoin currency to Money.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class BitcoinMoneyParser implements MoneyParser
{
    /**
     * @var int
     */
    private $fractionDigits;

    /**
     * @param int $fractionDigits
     */
    public function __construct($fractionDigits)
    {
        $this->fractionDigits = $fractionDigits;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($money, $forceCurrency = null)
    {
        if (is_string($money) === false) {
            throw new ParserException('Formatted raw money should be string, e.g. $1.00');
        }

        if (strpos($money, BitcoinCurrencies::SYMBOL) === false) {
            throw new ParserException('Value cannot be parsed as Bitcoin');
        }

        $decimal = str_replace(BitcoinCurrencies::SYMBOL, '', $money);
        $decimalSeparator = strpos($decimal, '.');

        if (false !== $decimalSeparator) {
            $lengthDecimal = strlen($decimal);
            $decimal = str_replace('.', '', $decimal);
            $decimal .= str_pad('', ($lengthDecimal - $decimalSeparator - $this->fractionDigits - 1) * -1, '0');
        } else {
            $decimal .= str_pad('', $this->fractionDigits, '0');
        }

        if (substr($decimal, 0, 1) === '-') {
            $decimal = '-'.ltrim(substr($decimal, 1), '0');
        } else {
            $decimal = ltrim($decimal, '0');
        }

        if ('' === $decimal) {
            $decimal = '0';
        }

        return new Money($decimal, new Currency(BitcoinCurrencies::CODE));
    }
}
