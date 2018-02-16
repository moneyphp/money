<?php

namespace Tests\Money\Formatter;

use Money\Formatter\AggregateMoneyFormatter;
use PHPUnit\Framework\TestCase;

final class AggregateMoneyFormatterTest extends TestCase
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
