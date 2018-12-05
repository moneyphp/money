<?php

namespace Money\Parser;

use Money\Currencies\BitcoinCurrencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\MoneyParser;

/**
 * Parses Bitcoin currency to Money.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class BitcoinMoneyParser implements MoneyParser
{
    use DecimalParserTrait;

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
        if (!is_string($money)) {
            throw new ParserException('Formatted raw money should be string, e.g. $1.00');
        }

        if (strpos($money, BitcoinCurrencies::SYMBOL) === false) {
            throw new ParserException('Value cannot be parsed as Bitcoin');
        }

        $currency = new Currency(BitcoinCurrencies::CODE);

        $decimal = str_replace(BitcoinCurrencies::SYMBOL, '', $money);

        $subunit = $this->fractionDigits;
        return $this->parseDecimal($decimal, $subunit, $currency);
    }
}
