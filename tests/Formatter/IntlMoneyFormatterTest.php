<?php

namespace Tests\Money\Formatter;

use Money\Currencies\ConstantSpecification;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;

final class IntlMoneyFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider numberFormatterExamples
     */
    public function testNumberFormatter($amount, $currency, $result, $mode, $hasPattern, $subunit, $fractionDigits)
    {
        $money = new Money($amount, (new Currency($currency))->withSubunit($subunit));

        $numberFormatter = new \NumberFormatter('en_US', $mode);

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
            [5005, 'USD', '$50', \NumberFormatter::CURRENCY, true, 2, 0],
            [100, 'USD', '$1.00', \NumberFormatter::CURRENCY, true, 2, 2],
            [41, 'USD', '$0.41', \NumberFormatter::CURRENCY, true, 2, 2],
            [5, 'USD', '$0.05', \NumberFormatter::CURRENCY, true, 2, 2],
            [5, 'USD', '$0.005', \NumberFormatter::CURRENCY, true, 3, 3],
            [35, 'USD', '$0.035', \NumberFormatter::CURRENCY, true, 3, 3],
            [135, 'USD', '$0.135', \NumberFormatter::CURRENCY, true, 3, 3],
            [6135, 'USD', '$6.135', \NumberFormatter::CURRENCY, true, 3, 3],
            [-6135, 'USD', '-$6.135', \NumberFormatter::CURRENCY, true, 3, 3],
            [-6152, 'USD', '-$6.2', \NumberFormatter::CURRENCY, true, 3, 1],
            [5, 'EUR', '€0.05', \NumberFormatter::CURRENCY, true, 2, 2],
            [50, 'EUR', '€0.50', \NumberFormatter::CURRENCY, true, 2, 2],
            [500, 'EUR', '€5.00', \NumberFormatter::CURRENCY, true, 2, 2],
            [5, 'EUR', '€0.05', \NumberFormatter::DECIMAL, true, 2, 2],
            [50, 'EUR', '€0.50', \NumberFormatter::DECIMAL, true, 2, 2],
            [500, 'EUR', '€5.00', \NumberFormatter::DECIMAL, true, 2, 2],
            [5, 'EUR', '5', \NumberFormatter::DECIMAL, false, 0, 0],
            [50, 'EUR', '50', \NumberFormatter::DECIMAL, false, 0, 0],
            [500, 'EUR', '500', \NumberFormatter::DECIMAL, false, 0, 0],
            [5, 'EUR', '500%', \NumberFormatter::PERCENT, false, 0, 0],
        ];
    }
}
