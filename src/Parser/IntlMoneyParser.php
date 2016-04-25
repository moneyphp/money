<?php

namespace Money\Parser;

use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;

/**
 * Parses a string into a Money object using intl extension.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class IntlMoneyParser implements MoneyParser
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
    public function parse($money, $forceCurrency = null)
    {
        if (!is_string($money)) {
            throw new ParserException('Formatted raw money should be string, e.g. $1.00');
        }

        $currency = null;
        $decimal = $this->formatter->parseCurrency($money, $currency);

        if ($decimal === false) {
            throw new ParserException(
                'Cannot parse '.$money.' to Money. '.$this->formatter->getErrorMessage()
            );
        }

        $decimal = (string) $decimal;

        if (strpos($decimal, '.') !== false) {
            $decimal = str_replace('.', '', $decimal);
        } else {
            $decimal .= str_pad('', $this->formatter->getAttribute(\NumberFormatter::FRACTION_DIGITS), '0');
        }

        if (substr($decimal, 0, 1) === '-') {
            $decimal = '-'.ltrim(substr($decimal, 1), '0');
        } else {
            $decimal = ltrim($decimal, '0');
        }

        if ($forceCurrency === null) {
            $forceCurrency = $currency;
        }

        return new Money($decimal, new Currency($forceCurrency));
    }
}
