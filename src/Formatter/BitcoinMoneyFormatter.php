<?php

namespace Money\Formatter;

use Money\Currencies\BitcoinCurrencies;
use Money\Money;
use Money\MoneyFormatter;

/**
 * Formats Money to Bitcoin currency.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class BitcoinMoneyFormatter implements MoneyFormatter
{
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
     * {@inheritdoc}
     */
    public function format(Money $money)
    {
        if (BitcoinCurrencies::CODE !== $money->getCurrency()->getCode()) {
            return $this->delegatedFormatter->format($money);
        }

        $valueBase = $money->getAmount();
        $negative = false;

        if ('-' === substr($valueBase, 0, 1)) {
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

        $subunits = BitcoinCurrencies::SYMBOL.$subunits;

        if (true === $negative) {
            $subunits = '-'.BitcoinCurrencies::SYMBOL.$subunits;
        }

        return $subunits;
    }
}
