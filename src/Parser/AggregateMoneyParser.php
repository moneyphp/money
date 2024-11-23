<?php

declare(strict_types=1);

namespace Money\Parser;

use Money\Currency;
use Money\Exception;
use Money\Money;
use Money\MoneyParser;

use function sprintf;

/**
 * Parses a string into a Money object using other parsers.
 */
final class AggregateMoneyParser implements MoneyParser
{
    /**
     * @param MoneyParser[] $parsers
     * @phpstan-param non-empty-array<MoneyParser> $parsers
     */
    public function __construct(private readonly array $parsers)
    {
    }

    public function parse(string $money, Currency|null $fallbackCurrency = null): Money
    {
        foreach ($this->parsers as $parser) {
            try {
                return $parser->parse($money, $fallbackCurrency);
            } catch (Exception\ParserException) {
            }
        }

        throw new Exception\ParserException(sprintf('Unable to parse %s', $money));
    }
}
