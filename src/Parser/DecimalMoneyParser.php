<?php

namespace Money\Parser;

use Money\Currencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\MoneyParser;

/**
 * Parses a decimal string into a Money object.
 *
 * @author Teoh Han Hui <teohhanhui@gmail.com>
 */
final class DecimalMoneyParser implements MoneyParser
{
    /**
     * @deprecated This stayed here for BC reasons.
     */
    const DECIMAL_PATTERN = '/^(?P<sign>-)?(?P<digits>0|[1-9]\d*)?\.?(?P<fraction>\d+)?$/';

    use DecimalParserTrait;

    /**
     * @var Currencies
     */
    private $currencies;

    /**
     * @param Currencies $currencies
     */
    public function __construct(Currencies $currencies)
    {
        $this->currencies = $currencies;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($money, $forceCurrency = null)
    {
        if (!is_string($money)) {
            throw new ParserException('Formatted raw money should be string, e.g. 1.00');
        }

        if (null === $forceCurrency) {
            throw new ParserException(
                'DecimalMoneyParser cannot parse currency symbols. Use forceCurrency argument'
            );
        }

        /*
         * This conversion is only required whilst currency can be either a string or a
         * Currency object.
         */
        $currency = $forceCurrency;
        if (!$currency instanceof Currency) {
            @trigger_error('Passing a currency as string is deprecated since 3.1 and will be removed in 4.0. Please pass a '.Currency::class.' instance instead.', E_USER_DEPRECATED);
            $currency = new Currency($currency);
        }

        $subunit = $this->currencies->subunitFor($currency);

        return $this->parseDecimal($money, $subunit, $currency);
    }
}
