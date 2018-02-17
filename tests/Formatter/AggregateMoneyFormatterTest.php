<?php

namespace Tests\Money\Formatter;

use Money\Formatter\AggregateMoneyFormatter;
use PHPUnit\Framework\TestCase;

final class AggregateMoneyFormatterTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_initialized_with_empty_array()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Initialize an empty Money\\Formatter\\AggregateMoneyFormatter is not possible');

        new AggregateMoneyFormatter([]);
    }
}
