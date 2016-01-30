<?php

namespace Tests\Money;

use Money\StringToUnitsParser;

final class StringsToUnitParserTest extends \PHPUnit_Framework_TestCase
{

    public static function provideStrings()
    {
        return [
            ['1000', 100000],
            ['1000.0', 100000],
            ['1000.00', 100000],
            ['0.01', 1],
            ['1', 100],
            ['-1000', -100000],
            ['-1000.0', -100000],
            ['-1000.00', -100000],
            ['-0.01', -1],
            ['-1', -100],
            ['+1000', 100000],
            ['+1000.0', 100000],
            ['+1000.00', 100000],
            ['+0.01', 1],
            ['+1', 100],
            ['.99', 99],
            ['-.99', -99],
        ];
    }

    /**
     * @dataProvider provideStrings
     */
    public function testStringToUnits($string, $units)
    {
        $parser = new StringToUnitsParser();
        $this->assertEquals($units, $parser->parse($string, 'USD')->getAmount());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCannotConvertStringToUnits()
    {
        $parser = new StringToUnitsParser();
        $parser->parse('THIS_IS_NOT_CONVERTABLE_TO_UNIT', 'USD');
    }

    public function testCannotConvertStringToUnitsWithoutCurrency()
    {
        $this->setExpectedException('Money\\ParserException');

        $parser = new StringToUnitsParser();
        $parser->parse('$ 100');
    }

}
