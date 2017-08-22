<?php

namespace Tests\Money\Formatter;

use Money\Formatter\AggregateMoneyFormatter;

final class AggregateMoneyFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function can_be_initialized_with_empty_array()
    {
        new AggregateMoneyFormatter([]);
    }
}
