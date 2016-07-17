<?php

namespace Tests\Money;

use Money\Currency;
use Money\Money;

final class MoneyTest extends \PHPUnit_Framework_TestCase
{
    public function testFactoryMethods()
    {
        $this->assertEquals(
            Money::EUR(25),
            Money::EUR(10)->add(Money::EUR(15))
        );

        $this->assertEquals(
            Money::USD(25),
            Money::USD(10)->add(Money::USD(15))
        );
    }

    public function testJsonEncoding()
    {
        $this->assertEquals(
            '{"amount":"350","currency":"EUR"}',
            json_encode(Money::EUR(350))
        );
    }

    public function testMaxInit()
    {
        $one = new Money(1, new Currency('EUR'));

        $this->assertInstanceOf('Money\\Money', new Money(PHP_INT_MAX, new Currency('EUR')));
        $this->assertInstanceOf('Money\\Money', (new Money(PHP_INT_MAX, new Currency('EUR')))->add($one));
        $this->assertInstanceOf('Money\\Money', (new Money(PHP_INT_MAX, new Currency('EUR')))->subtract($one));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateFromFloatError()
    {
        Money::fromFloat('1.23456789', new Currency('XXX', 2));
    }

    /**
     * @dataProvider getDataForNormalize
     *
     * @param string $expected
     * @param string $input
     */
    public function testNormalize($expected, $input)
    {
        self::assertEquals($expected, Money::normalize($input));
    }

    /**
     * @return array
     */
    public function getDataForNormalize()
    {
        return [
            ['0', '0'],
            ['10', '10'],
            ['1', '1'],
            ['1', '1.'],
            ['0.1', '.1'],
            ['0', '.'],
            ['0', '00.00'],
        ];
    }

    /**
     * @dataProvider getDataForCreateFromFloat
     *
     * @param string       $expected
     * @param string|float $amount
     * @param int          $numberOfSubunits
     */
    public function testCreateFromFloat($expected, $amount, $numberOfSubunits)
    {
        $money = Money::fromFloat($amount, new Currency('XXX', $numberOfSubunits));

        self::assertEquals($expected, (string) $money);
    }

    /**
     * @return array
     */
    public function getDataForCreateFromFloat()
    {
        return [
            ['1 XXX', 1, 0],
            ['1 XXX', 1.0, 0],
            ['1 XXX', '1.0', 0],

            ['1.23456789 XXX', '1.23456789', 10],
            ['-1.23456789 XXX', '-1.23456789', 10],
        ];
    }
}
