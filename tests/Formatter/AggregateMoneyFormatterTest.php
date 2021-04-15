<?php

declare(strict_types=1);

namespace Tests\Money\Formatter;

use InvalidArgumentException;
use Money\Formatter\AggregateMoneyFormatter;
use PHPUnit\Framework\TestCase;

final class AggregateMoneyFormatterTest extends TestCase
{
    /**
     * @test
     */
    public function itCanBeInitializedWithEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Initialize an empty Money\\Formatter\\AggregateMoneyFormatter is not possible');

        new AggregateMoneyFormatter([]);
    }
}
