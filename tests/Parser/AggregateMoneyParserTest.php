<?php

namespace Tests\Money\Parser;

use Money\Parser\AggregateMoneyParser;
use PHPUnit\Framework\TestCase;

final class AggregateMoneyParserTest extends TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Initialize an empty Money\Parser\AggregateMoneyParser is not possible
     */
    public function can_be_initialized_with_empty_array()
    {
        new AggregateMoneyParser([]);
    }
}
