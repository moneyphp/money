<?php

namespace Money\Formatter;

use Money\CurrenciesSpecification;
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
     * @var CurrenciesSpecification
     */
    private $specifier;

    /**
     * @param \NumberFormatter        $formatter
     * @param CurrenciesSpecification $specifier
     */
    public function __construct(\NumberFormatter $formatter, CurrenciesSpecification $specifier)
    {
        $this->formatter = $formatter;
        $this->specifier = $specifier;
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

        $subunit = $this->specifier->specify($money->getCurrency())->getSubunit();
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
