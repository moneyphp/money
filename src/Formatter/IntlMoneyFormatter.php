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
     * @var int
     */
    private $subunits;

    /**
     * @param \NumberFormatter $formatter
     * @param int              $subunits
     */
    public function __construct(\NumberFormatter $formatter, $subunits = 0)
    {
        if (is_int($subunits) === false) {
            throw new \InvalidArgumentException('Subunits must be an integer');
        }

        $this->formatter = $formatter;
        $this->subunits = $subunits;
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

        $valueLength = strlen($valueBase);

        if ($valueLength > $this->subunits) {
            $number = substr($valueBase, 0, $valueLength - $this->subunits).'.';
            $number .= substr($valueBase, $valueLength - $this->subunits);
        } else {
            $number = '0.'.str_pad('', $this->subunits - $valueLength, '0').$valueBase;
        }

        if ($negative === true) {
            $number = '-'.$number;
        }

        return $this->formatter->formatCurrency($number, $money->getCurrency()->getCode());
    }
}
