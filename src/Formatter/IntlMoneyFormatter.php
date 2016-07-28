<?php

namespace Money\Formatter;

use Money\Currencies\CurrenciesWithSubunit;
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
     * @var CurrenciesWithSubunit
     */
    private $currenciesWithSubunit;

    /**
     * @param \NumberFormatter      $formatter
     * @param CurrenciesWithSubunit $currenciesWithSubunit
     */
    public function __construct(\NumberFormatter $formatter, CurrenciesWithSubunit $currenciesWithSubunit)
    {
        $this->formatter = $formatter;
        $this->currenciesWithSubunit = $currenciesWithSubunit;
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

        $subunits = $this->currenciesWithSubunit->getSubunitsFor($money->getCurrency());
        $valueLength = strlen($valueBase);

        if ($valueLength > $subunits) {
            $formatted = substr($valueBase, 0, $valueLength - $subunits).'.';
            $formatted .= substr($valueBase, $valueLength - $subunits);
        } else {
            $formatted = '0.'.str_pad('', $subunits - $valueLength, '0').$valueBase;
        }

        if ($negative === true) {
            $formatted = '-'.$formatted;
        }

        return $this->formatter->formatCurrency($formatted, $money->getCurrency()->getCode());
    }
}
