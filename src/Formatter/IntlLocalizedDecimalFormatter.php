<?php

declare(strict_types=1);

namespace Money\Formatter;

use Money\Currencies;
use Money\Money;
use Money\MoneyFormatter;
use NumberFormatter;

use function str_pad;
use function strlen;
use function substr;

/**
 * Formats a Money object using intl extension.
 */
final class IntlLocalizedDecimalFormatter implements MoneyFormatter
{
    private NumberFormatter $formatter;

    private Currencies $currencies;

    public function __construct(NumberFormatter $formatter, Currencies $currencies)
    {
        $this->formatter  = $formatter;
        $this->currencies = $currencies;
    }

    public function format(Money $money): string
    {
        $valueBase = $money->getAmount();
        $negative  = false;

        if ($valueBase[0] === '-') {
            $negative  = true;
            $valueBase = substr($valueBase, 1);
        }

        $subunit     = $this->currencies->subunitFor($money->getCurrency());
        $valueLength = strlen($valueBase);

        if ($valueLength > $subunit) {
            $formatted     = substr($valueBase, 0, $valueLength - $subunit);
            $decimalDigits = substr($valueBase, $valueLength - $subunit);

            if (strlen($decimalDigits) > 0) {
                $formatted .= '.' . $decimalDigits;
            }
        } else {
            $formatted = '0.' . str_pad('', $subunit - $valueLength, '0') . $valueBase;
        }

        if ($negative === true) {
            $formatted = '-' . $formatted;
        }

        return $this->formatter->format((float) $formatted);
    }
}
