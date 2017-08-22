<?php

namespace Tests\Money\Parser;

use Money\Parser\AggregateMoneyParser;

final class AggregateMoneyParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function can_be_initialized_with_empty_array()
    {
        new AggregateMoneyParser([]);
    }
}
