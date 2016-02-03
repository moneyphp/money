<?php

namespace Money;

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
    public function parse($formattedMoney, $forceCurrency = null)
    {
        $decimal = $this->formatter->parseCurrency($formattedMoney, $currency);
        if ($decimal === false) {
            throw new ParserException(
                'Cannot parse '.$formattedMoney.' to Money. '.$this->formatter->getErrorMessage()
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
            return new Money($decimal, new Currency($currency));
        }

        return new Money($decimal, new Currency($forceCurrency));
    }
}
