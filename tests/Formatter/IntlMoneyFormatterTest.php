<?php

namespace Tests\Money\Formatter;

use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;

final class IntlMoneyFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider numberFormatterExamples
     */
    public function testNumberFormatter($amount, $currency, $result, $locale, $mode, $hasPattern, $fractionDigits)
    {
        $money = new Money($amount, new Currency($currency));

        $numberFormatter = new \NumberFormatter($locale, $mode);

        if (true === $hasPattern) {
            $numberFormatter->setPattern('¤#,##0.00;-¤#,##0.00');
        }

        $numberFormatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $fractionDigits);

        $moneyFormatter = new IntlMoneyFormatter($numberFormatter);
        $this->assertEquals($result, $moneyFormatter->format($money));
    }

    public static function numberFormatterExamples()
    {
        return [
            [100, 'USD', '$1.00', 'en_US', \NumberFormatter::CURRENCY, true, 2],
            [41, 'USD', '$0.41', 'en_US', \NumberFormatter::CURRENCY, true, 2],
            [5, 'USD', '$0.05', 'en_US', \NumberFormatter::CURRENCY, true, 2],
            [5, 'USD', '$0.005', 'en_US', \NumberFormatter::CURRENCY, true, 3],
            [35, 'USD', '$0.035', 'en_US', \NumberFormatter::CURRENCY, true, 3],
            [135, 'USD', '$0.135', 'en_US', \NumberFormatter::CURRENCY, true, 3],
            [6135, 'USD', '$6.135', 'en_US', \NumberFormatter::CURRENCY, true, 3],
            [-6135, 'USD', '-$6.135', 'en_US', \NumberFormatter::CURRENCY, true, 3],
            [5, 'EUR', '€0.05', 'en_US', \NumberFormatter::CURRENCY, true, 2],
            [50, 'EUR', '€0.50', 'en_US', \NumberFormatter::CURRENCY, true, 2],
            [500, 'EUR', '€5.00', 'en_US', \NumberFormatter::CURRENCY, true, 2],
            [5, 'EUR', '€0.05', 'en_US', \NumberFormatter::DECIMAL, true, 2],
            [50, 'EUR', '€0.50', 'en_US', \NumberFormatter::DECIMAL, true, 2],
            [500, 'EUR', '€5.00', 'en_US', \NumberFormatter::DECIMAL, true, 2],
            [5, 'EUR', '5', 'en_US', \NumberFormatter::DECIMAL, false, 0],
            [50, 'EUR', '50', 'en_US', \NumberFormatter::DECIMAL, false, 0],
            [500, 'EUR', '500', 'en_US', \NumberFormatter::DECIMAL, false, 0],
            [5, 'EUR', '500%', 'en_US', \NumberFormatter::PERCENT, false, 0],
        ];
    }
}
