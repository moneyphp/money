<?php

namespace Money\Formatter;

use Money\Money;
use Money\MoneyFormatter;
use Money\SubUnitProvider;

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
     * @var SubUnitProvider
     */
    private $subUnitProvider;

    /**
     * @param \NumberFormatter $formatter
     * @param SubUnitProvider  $subUnitProvider
     */
    public function __construct(\NumberFormatter $formatter, SubUnitProvider $subUnitProvider)
    {
        $this->formatter = $formatter;
        $this->subUnitProvider = $subUnitProvider;
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

        $subunits = $this->subUnitProvider->provide($money->getCurrency());
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
