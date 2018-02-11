<?php

namespace Tests\Money\Formatter;

use Money\Currencies;
use Money\Currency;
use Money\Formatter\IntlLocalizedDecimalFormatter;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

final class IntlLocalizedDecimalFormatterTest extends TestCase
{
    /**
     * @dataProvider moneyExamples
     * @test
     */
    public function it_formats_money($amount, $currency, $subunit, $result, $mode, $fractionDigits)
    {
        $money = new Money($amount, new Currency($currency));

        $numberFormatter = new \NumberFormatter('en_US', $mode);

        $numberFormatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $fractionDigits);

        $currencies = $this->prophesize(Currencies::class);

        $currencies->subunitFor(Argument::allOf(
            Argument::type(Currency::class),
            Argument::which('getCode', $currency)
        ))->willReturn($subunit);

        $moneyFormatter = new IntlLocalizedDecimalFormatter($numberFormatter, $currencies->reveal());
        $this->assertSame($result, $moneyFormatter->format($money));
    }

    public static function moneyExamples()
    {
        return [
            [5005, 'USD', 2, '50', \NumberFormatter::DECIMAL, 0],
            [100, 'USD', 2, '1.00', \NumberFormatter::DECIMAL, 2],
            [41, 'USD', 2, '0.41', \NumberFormatter::DECIMAL, 2],
            [5, 'USD', 2, '0.05', \NumberFormatter::DECIMAL, 2],
            [5, 'USD', 2, '0.050', \NumberFormatter::DECIMAL, 3],
            [35, 'USD', 2, '0.350', \NumberFormatter::DECIMAL, 3],
            [135, 'USD', 2, '1.350', \NumberFormatter::DECIMAL, 3],
            [6135, 'USD', 2, '61.350', \NumberFormatter::DECIMAL, 3],
            [-6135, 'USD', 2, '-61.350', \NumberFormatter::DECIMAL, 3],
            [-6152, 'USD', 2, '-61.5', \NumberFormatter::DECIMAL, 1],
            [5, 'EUR', 2, '0.05', \NumberFormatter::DECIMAL, 2],
            [50, 'EUR', 2, '0.50', \NumberFormatter::DECIMAL, 2],
            [500, 'EUR', 2, '5.00', \NumberFormatter::DECIMAL, 2],
            [5, 'EUR', 2, '0.05', \NumberFormatter::DECIMAL, 2],
            [50, 'EUR', 2, '0.50', \NumberFormatter::DECIMAL, 2],
            [500, 'EUR', 2, '5.00', \NumberFormatter::DECIMAL, 2],
            [5, 'EUR', 2, '0', \NumberFormatter::DECIMAL, 0],
            [50, 'EUR', 2, '0', \NumberFormatter::DECIMAL, 0],
            [500, 'EUR', 2, '5', \NumberFormatter::DECIMAL, 0],
            [5055, 'USD', 2, '51', \NumberFormatter::DECIMAL, 0],
        ];
    }
}
