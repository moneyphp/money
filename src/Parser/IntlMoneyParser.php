<?php

namespace Money\Parser;

use Money\Currencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\MoneyParser;

/**
 * Parses a string into a Money object using intl extension.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class IntlMoneyParser implements MoneyParser
{
    use DecimalParserTrait;

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

        $decimal = (string) $decimal;
        $subunit = $this->currencies->subunitFor($currency);

        return $this->parseDecimal($decimal, $subunit, $currency);
    }
}
