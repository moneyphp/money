<?php

declare(strict_types=1);

namespace Tests\Money\Formatter;

use Money\Currencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

final class DecimalMoneyFormatterTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @psalm-param non-empty-string $currency
     * @psalm-param positive-int|0 $subunit
     * @psalm-param numeric-string $result
     *
     * @dataProvider moneyExamples
     * @test
     */
    public function itFormatsMoney(int $amount, string $currency, int $subunit, string $result): void
    {
        $money = new Money($amount, new Currency($currency));

        $currencies = $this->prophesize(Currencies::class);

        $currencies->subunitFor(Argument::allOf(
            Argument::type(Currency::class),
            Argument::which('getCode', $currency)
        ))->willReturn($subunit);

        $moneyFormatter = new DecimalMoneyFormatter($currencies->reveal());
        $this->assertSame($result, $moneyFormatter->format($money));
    }

    /**
     * @psalm-return non-empty-list<array{
     *     int,
     *     non-empty-string,
     *     positive-int|0,
     *     numeric-string
     * }>
     */
    public static function moneyExamples()
    {
        return [
            [5005, 'USD', 2, '50.05'],
            [100, 'USD', 2, '1.00'],
            [41, 'USD', 2, '0.41'],
            [5, 'USD', 2, '0.05'],
            [50, 'USD', 3, '0.050'],
            [350, 'USD', 3, '0.350'],
            [1357, 'USD', 3, '1.357'],
            [61351, 'USD', 3, '61.351'],
            [-61351, 'USD', 3, '-61.351'],
            [-6152, 'USD', 2, '-61.52'],
            [5, 'JPY', 0, '5'],
            [50, 'JPY', 0, '50'],
            [500, 'JPY', 0, '500'],
            [-5055, 'JPY', 0, '-5055'],
            [5, 'JPY', 2, '0.05'],
            [50, 'JPY', 2, '0.50'],
            [500, 'JPY', 2, '5.00'],
            [-5055, 'JPY', 2, '-50.55'],
            [50050050, 'USD', 2, '500500.50'],
        ];
    }
}
