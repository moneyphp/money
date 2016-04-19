<?php

namespace Money\Parser;

use Money\Currency;
use Money\Formatter\BitcoinSupportedMoneyFormatter;
use Money\Money;
use Money\MoneyParser;

/**
 * Parses Bitcoin currency to Money.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class BitcoinSupportedMoneyParser implements MoneyParser
{
    const SYMBOL = "\0xC9\0x83";

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
        if (strpos($money, self::SYMBOL) === false) {
            return $this->delegatedParser->parse($money, $forceCurrency);
        }

        $decimal = str_replace(self::SYMBOL, '', $money);
        $decimalSeparator = strpos($decimal, '.');
        if ($decimalSeparator !== false) {
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

        if ($decimal === '') {
            $decimal = '0';
        }

        return new Money($decimal, new Currency(BitcoinSupportedMoneyFormatter::CODE));
    }
}
