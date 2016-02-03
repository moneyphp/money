<?php

namespace Money\Formatter;

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

        if (substr($valueBase, 0, 1) === '-') {
            $negative = true;
            $valueBase = substr($valueBase, 1);
        }

        $fractionDigits = $this->formatter->getAttribute(\NumberFormatter::FRACTION_DIGITS);
        $valueLength = strlen($valueBase);

        if ($valueLength > $fractionDigits) {
            $subunits = substr($valueBase, 0, $valueLength - $fractionDigits).'.';
            $subunits .= substr($valueBase, $valueLength - $fractionDigits);
        } else {
            $subunits = '0.'.str_pad('', $fractionDigits - $valueLength, '0').$valueBase;
        }

        if ($negative === true) {
            $subunits = '-'.$subunits;
        }

        return $this->formatter->formatCurrency($subunits, $money->getCurrency()->getCode());
    }
}
