<?php

namespace Money\Formatter;

use Money\Money;
use Money\MoneyFormatter;
use Money\Parser\BitcoinSupportedMoneyParser;

/**
 * Formats Money to Bitcoin currency.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class BitcoinSupportedMoneyFormatter implements MoneyFormatter
{
    const CODE = 'XBT';

    /**
     * @var MoneyFormatter
     */
    private $delegatedFormatter;

    /**
     * @var int
     */
    private $fractionDigits;

    /**
     * @param MoneyFormatter $innerFormatter
     * @param int            $fractionDigits
     */
    public function __construct(MoneyFormatter $innerFormatter, $fractionDigits)
    {
        $this->delegatedFormatter = $innerFormatter;
        $this->fractionDigits = $fractionDigits;
    }

    /**
     * Formats a Money object as string.
     *
     * @param Money $money
     *
     * @return string
     */
    public function format(Money $money)
    {
        if ($money->getCurrency()->getCode() !== self::CODE) {
            return $this->delegatedFormatter->format($money);
        }

        $valueBase = (string) $money->getAmount();
        $negative = false;

        if (substr($valueBase, 0, 1) === '-') {
            $negative = true;
            $valueBase = substr($valueBase, 1);
        }

        $fractionDigits = $this->fractionDigits;
        $valueLength = strlen($valueBase);

        if ($valueLength > $fractionDigits) {
            $subunits = substr($valueBase, 0, $valueLength - $fractionDigits);
            if ($fractionDigits) {
                $subunits .= '.';
                $subunits .= substr($valueBase, $valueLength - $fractionDigits);
            }
        } else {
            $subunits = '0.'.str_pad('', $fractionDigits - $valueLength, '0').$valueBase;
        }

        if ($negative === true) {
            $subunits = '-'.BitcoinSupportedMoneyParser::SYMBOL.$subunits;
        } else {
            $subunits = BitcoinSupportedMoneyParser::SYMBOL.$subunits;
        }

        return $subunits;
    }
}
