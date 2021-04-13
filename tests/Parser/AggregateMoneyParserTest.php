<?php

namespace Tests\Money\Parser;

use InvalidArgumentException;
use Money\Parser\AggregateMoneyParser;
use PHPUnit\Framework\TestCase;

final class AggregateMoneyParserTest extends TestCase
{
    /**
     * @test
     */
    public function itCanBeInitializedWithEmptyArray()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Initialize an empty Money\\Parser\\AggregateMoneyParser is not possible');

        new AggregateMoneyParser([]);
    }
}
