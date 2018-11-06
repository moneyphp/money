<?php

namespace Tests\Money;

use Money\Currency;
use Money\Money;
use Money\MoneyRange;
use PHPUnit\Framework\TestCase;

final class MoneyRangeTest extends TestCase
{
    use AggregateExamples, RoundExamples;

    const START_AMOUNT = 10;

    const END_AMOUNT = 20;

    const OTHER_AMOUNT = 5;

    const CURRENCY = 'EUR';

    const OTHER_CURRENCY = 'USD';

    /**
     * @dataProvider equalityExamples
     * @test
     */
    public function it_equals_to_another_money_range($startAmount, $endAmount, $currency, $equality)
    {
        $range = new MoneyRange(
            new Money(self::START_AMOUNT, new Currency(self::CURRENCY)),
            new Money(self::END_AMOUNT, new Currency(self::CURRENCY))
        );

        $this->assertEquals(
            $equality,
            $range->equals(
                new MoneyRange(
                    new Money($startAmount, $currency),
                    new Money($endAmount, $currency)
                )
            )
        );
    }

    /**
     * @dataProvider comparisonExamples
     * @test
     */
    public function it_compares_to_values($amount, $result)
    {
        $currency = new Currency(self::CURRENCY);

        $range = new MoneyRange(
            new Money(self::START_AMOUNT, $currency),
            new Money(self::END_AMOUNT, $currency)
        );

        $value = new Money($amount, $currency);

        $this->assertEquals(1 === $result, $range->greaterThan($value));
        $this->assertEquals(-1 === $result, $range->lessThan($value));
    }

    /**
     * @dataProvider invalidOperandExamples
     * @test
     */
    public function it_throws_an_exception_when_operand_is_invalid_during_multiplication($operand)
    {
        $this->expectException(\InvalidArgumentException::class);

        $currency = new Currency(self::CURRENCY);

        $range = new MoneyRange(
            new Money(1, $currency),
            new Money(2, $currency)
        );

        $range->multiply($operand);
    }

    /**
     * @dataProvider invalidOperandExamples
     * @test
     */
    public function it_throws_an_exception_when_operand_is_invalid_during_division($operand)
    {
        $this->expectException(\InvalidArgumentException::class);

        $currency = new Currency(self::CURRENCY);

        $range = new MoneyRange(
            new Money(1, $currency),
            new Money(2, $currency)
        );

        $range->divide($operand);
    }

    /**
     * @dataProvider absoluteExamples
     * @test
     */
    public function it_calculates_the_absolute_value($startAmount, $endAmount, $result)
    {
        $currency = new Currency(self::CURRENCY);

        $range = new MoneyRange(
            new Money($startAmount, $currency),
            new Money($endAmount, $currency)
        );

        $range = $range->absolute();

        $this->assertEquals($result, $range);
    }

    /**
     * @dataProvider midPointExamples
     * @test
     */
    public function it_calculates_the_mid_point($startAmount, $endAmount, $result)
    {
        $currency = new Currency(self::CURRENCY);

        $range = new MoneyRange(
            new Money($startAmount, $currency),
            new Money($endAmount, $currency)
        );

        $midPoint = $range->midPoint();

        $this->assertEquals($result, $midPoint->getAmount());
    }

    /**
     * @test
     */
    public function it_converts_to_json()
    {
        $this->assertEquals(
            '{"start":"100","end":"350","currency":"EUR"}',
            json_encode(MoneyRange::EUR(100,350))
        );
    }

    public function equalityExamples()
    {
        return [
            [self::START_AMOUNT, self::END_AMOUNT, new Currency(self::CURRENCY), true],
            [self::START_AMOUNT + 1, self::END_AMOUNT, new Currency(self::CURRENCY), false],
            [self::START_AMOUNT, self::END_AMOUNT + 1, new Currency(self::CURRENCY), false],
            [self::START_AMOUNT, self::END_AMOUNT, new Currency(self::OTHER_CURRENCY), false],
            [self::START_AMOUNT + 1, self::END_AMOUNT, new Currency(self::OTHER_CURRENCY), false],
            [self::START_AMOUNT, self::END_AMOUNT + 1, new Currency(self::OTHER_CURRENCY), false],
            [(string) self::START_AMOUNT, self::END_AMOUNT, new Currency(self::CURRENCY), true],
            [((string) self::START_AMOUNT).'.000', self::END_AMOUNT, new Currency(self::CURRENCY), true],
        ];
    }

    public function comparisonExamples()
    {
        return [
            [self::START_AMOUNT, 0],
            [self::START_AMOUNT - 1, 1],
            [self::END_AMOUNT + 1, -1],
        ];
    }

    public function invalidOperandExamples()
    {
        return [
            [[]],
            [false],
            ['operand'],
            [null],
            [new \stdClass()],
        ];
    }

    public function absoluteExamples()
    {
        $currency = new Currency(self::CURRENCY);

        return [
            [1, 2, new MoneyRange(new Money(1, $currency), new Money(2, $currency))],
            [0, 0, new MoneyRange(new Money(0, $currency), new Money(0, $currency))],
            [-1, 1, new MoneyRange(new Money(1, $currency), new Money(1, $currency))],
            [-2, -1, new MoneyRange(new Money(1, $currency), new Money(2, $currency))],
        ];
    }

    public function midPointExamples()
    {
        return [
            [10, 20, 15],
            [10, 10, 10],
            [0, 0, 0],
            [-20, -10, -15],
            [-10, 10, 0],
        ];
    }
}
