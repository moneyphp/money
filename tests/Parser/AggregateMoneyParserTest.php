<?php

namespace Tests\Money\Parser;

use Money\Parser\AggregateMoneyParser;
use PHPUnit\Framework\TestCase;

final class AggregateMoneyParserTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_initialized_with_empty_array()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Initialize an empty Money\\Parser\\AggregateMoneyParser is not possible');

        new AggregateMoneyParser([]);
    }
}
