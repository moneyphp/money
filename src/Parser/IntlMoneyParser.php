<?php

namespace Money\Parser;

use Money\Currencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;
use Money\Number;

/**
 * Parses a string into a Money object using intl extension.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class IntlMoneyParser implements MoneyParser
{
    const DECIMAL_PATTERN = '/^(?P<sign>-)?(?P<digits>0|[1-9]\d*)?\.?(?P<fraction>\d+)?$/';

    /**
     * @var \NumberFormatter
     */
    private $formatter;

    /**
     * @var Currencies
     */
    private $currencies;

    /**
     * @param \NumberFormatter $formatter
     * @param Currencies       $currencies
     */
    public function __construct(\NumberFormatter $formatter, Currencies $currencies)
    {
        $this->formatter = $formatter;
        $this->currencies = $currencies;
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

        if (false === $decimal) {
            throw new ParserException(
                'Cannot parse '.$money.' to Money. '.$this->formatter->getErrorMessage()
            );
        }

        if (null !== $forceCurrency) {
            $currency = $forceCurrency;

            /*
             * This conversion is only required whilst currency can be either a string or a
             * Currency object.
             */
            if (!$currency instanceof Currency) {
                @trigger_error('Passing a currency as string is deprecated since 3.1 and will be removed in 4.0. Please pass a '.Currency::class.' instance instead.', E_USER_DEPRECATED);
                $currency = new Currency($currency);
            }
        } else {
            $currency = new Currency($currency);
        }

        $decimal = trim((string) $decimal);
        if ($decimal === '') {
            return new Money(0, $currency);
        }

        $subunit = $this->currencies->subunitFor($currency);

        if (!preg_match(self::DECIMAL_PATTERN, $decimal, $matches) || !isset($matches['digits'])) {
            throw new ParserException(sprintf(
                'Cannot parse "%s" to Money.',
                $decimal
            ));
        }

        $negative = isset($matches['sign']) && $matches['sign'] === '-';

        $decimal = $matches['digits'];

        if ($negative) {
            $decimal = '-'.$decimal;
        }

        if (isset($matches['fraction'])) {
            $fractionDigits = strlen($matches['fraction']);
            $decimal .= $matches['fraction'];
            $decimal = Number::roundMoneyValue($decimal, $subunit, $fractionDigits);

            if ($fractionDigits > $subunit) {
                $decimal = substr($decimal, 0, $subunit - $fractionDigits);
            } elseif ($fractionDigits < $subunit) {
                $decimal .= str_pad('', $subunit - $fractionDigits, '0');
            }
        } else {
            $decimal .= str_pad('', $subunit, '0');
        }

        if ($negative) {
            $decimal = '-'.ltrim(substr($decimal, 1), '0');
        } else {
            $decimal = ltrim($decimal, '0');
        }

        if ($decimal === '' || $decimal === '-') {
            $decimal = '0';
        }

        return new Money($decimal, $currency);
    }
}
