<?php

namespace Money\Parser;

use Money\Currencies\BitcoinCurrencies;
use Money\Currency;
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
     * @var MoneyParser
     */
    private $delegatedParser;

    /**
     * @var int
     */
    private $fractionDigits;

    /**
     * @param MoneyParser $innerParser
     * @param int         $fractionDigits
     */
    public function __construct(MoneyParser $innerParser, $fractionDigits)
    {
        $this->delegatedParser = $innerParser;
        $this->fractionDigits = $fractionDigits;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($money, $forceCurrency = null)
    {
        if (false === strpos($money, BitcoinCurrencies::SYMBOL)) {
            return $this->delegatedParser->parse($money, $forceCurrency);
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

        if ('-' === substr($decimal, 0, 1)) {
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
