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
     * @var int|null
     */
    private $subunits;

    /**
     * @param \NumberFormatter $formatter
     */
    public function __construct(\NumberFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @param \NumberFormatter $formatter
     * @param int              $subunits
     * @return IntlMoneyFormatter
     */
    public static function withSubunits(\NumberFormatter $formatter, $subunits)
    {
        $instance = new self($formatter);
        $instance->subunits = $subunits;
        return $instance;
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
        if ($this->subunits === null) {
            $subunits = $this->formatter->getAttribute(\NumberFormatter::FRACTION_DIGITS);
        } else {
            $subunits = $this->subunits;
        }

        if ($valueLength > $subunits) {
            $number = substr($valueBase, 0, $valueLength - $subunits).'.';
            $number .= substr($valueBase, $valueLength - $subunits);
        } else {
            $number = '0.'.str_pad('', $subunits - $valueLength, '0').$valueBase;
        }

        if ($negative === true) {
            $number = '-'.$number;
        }

        return $this->formatter->formatCurrency($number, $money->getCurrency()->getCode());
    }
}
