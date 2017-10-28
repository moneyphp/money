<?php

namespace Tests\Money\Formatter;

use Money\Formatter\AggregateMoneyFormatter;

final class AggregateMoneyFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Initialize an empty Money\Formatter\AggregateMoneyFormatter is not possible
     */
    public function can_be_initialized_with_empty_array()
    {
        new AggregateMoneyFormatter([]);
    }
}
