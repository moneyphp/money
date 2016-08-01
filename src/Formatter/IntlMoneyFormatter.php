<?php

namespace Money\Formatter;

use Money\Currencies;
use Money\Money;
use Money\MoneyFormatter;

/**
 * Formats a Money object using intl extension.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class IntlMoneyFormatter implements MoneyFormatter
{
    /**
     * @var \NumberFormatter
     */
    private $formatter;

    /**
     * @param \NumberFormatter $formatter
     */
    public function __construct(\NumberFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function format(Money $money)
    {
        $valueBase = (string) $money->getAmount();
        $negative = false;

        if ($valueBase[0] === '-') {
            $negative = true;
            $valueBase = substr($valueBase, 1);
        }

        $subunit = $money->getCurrency()->getSubunit();
        $valueLength = strlen($valueBase);

        if ($valueLength > $subunit) {
            $formatted = substr($valueBase, 0, $valueLength - $subunit).'.';
            $formatted .= substr($valueBase, $valueLength - $subunit);
        } else {
            $formatted = '0.'.str_pad('', $subunit - $valueLength, '0').$valueBase;
        }

        if ($negative === true) {
            $formatted = '-'.$formatted;
        }

        return $this->formatter->formatCurrency($formatted, $money->getCurrency()->getCode());
    }
}
