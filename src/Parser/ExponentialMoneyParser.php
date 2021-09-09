<?php

declare(strict_types=1);

namespace Money\Parser;

use Money\Currencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;
use Money\Number;

use function trim;
use function preg_match;
use function sprintf;
use function number_format;
use function str_pad;

/**
 * Parses an exponential string into a Money object.
 *
 * @author George Mponos <gmponos@gmail.com>
 */
final class ExponentialMoneyParser implements MoneyParser
{
    private const EXPO_DECIMAL_PATTERN = '/^(?P<sign>-)?(?P<digits>0|[1-9]\d*)?\.?(?P<fraction>\d+)?[eE][-+]\d+$/';

    private const DECIMAL_PATTERN = '/^(?P<sign>-)?(?P<digits>0|[1-9]\d*)?\.?(?P<fraction>\d+)?$/';

    /** @var Currencies */
    private $currencies;

    public function __construct(Currencies $currencies)
    {
        $this->currencies = $currencies;
    }

    public function parse(string $money, Currency|null $fallbackCurrency = null): Money
    {
        if ($fallbackCurrency === null) {
            throw new ParserException(
                'ExponentialMoneyParser cannot parse currency symbols. Use fallbackCurrency argument'
            );
        }

        $currency = $fallbackCurrency;

        $expo = trim($money);
        if ($expo === '') {
            return new Money(0, $currency);
        }

        $subunit = $this->currencies->subunitFor($currency);

        if (! preg_match(self::EXPO_DECIMAL_PATTERN, $expo, $matches) || ! isset($matches['digits'])) {
            throw new ParserException(sprintf(
                'Cannot parse "%s" to Money.',
                $expo
            ));
        }

        $number = number_format($expo, $subunit, '.', '');
        if (! preg_match(self::DECIMAL_PATTERN, $number, $matches) || ! isset($matches['digits'])) {
            throw new ParserException(sprintf(
                'Cannot parse "%s" to Money.',
                $expo
            ));
        }

        $negative = isset($matches['sign']) && $matches['sign'] === '-';

        $decimal = $matches['digits'];

        if ($negative) {
            $decimal = '-' . $decimal;
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
            $decimal = '-' . ltrim(substr($decimal, 1), '0');
        } else {
            $decimal = ltrim($decimal, '0');
        }

        if ($decimal === '' || $decimal === '-') {
            $decimal = '0';
        }

        return new Money($decimal, $currency);
    }
}
