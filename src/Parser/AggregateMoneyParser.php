<?php

declare(strict_types=1);

namespace Money\Parser;

use InvalidArgumentException;
use Money\Currency;
use Money\Exception;
use Money\Money;
use Money\MoneyParser;

use function sprintf;
use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * Parses a string into a Money object using other parsers.
 */
final class AggregateMoneyParser implements MoneyParser
{
    /**
     * @var MoneyParser[]
     * @psalm-var non-empty-array<MoneyParser>
     */
    private array $parsers = [];

    /**
     * @param MoneyParser[] $parsers
     * @psalm-param non-empty-array<MoneyParser> $parsers
     */
    public function __construct(array $parsers)
    {
        if (empty($parsers)) {
            throw new InvalidArgumentException(sprintf('Initialize an empty %s is not possible', self::class));
        }

        foreach ($parsers as $parser) {
            if ($parser instanceof MoneyParser === false) {
                throw new InvalidArgumentException('All parsers must implement ' . MoneyParser::class);
            }

            $this->parsers[] = $parser;
        }
    }

    public function parse(string $money, Currency|null $forceCurrency = null): Money
    {
        if ($forceCurrency !== null && ! $forceCurrency instanceof Currency) {
            @trigger_error('Passing a currency as string is deprecated since 3.1 and will be removed in 4.0. Please pass a ' . Currency::class . ' instance instead.', E_USER_DEPRECATED);
            $forceCurrency = new Currency($forceCurrency);
        }

        foreach ($this->parsers as $parser) {
            try {
                return $parser->parse($money, $forceCurrency);
            } catch (Exception\ParserException $e) {
            }
        }

        throw new Exception\ParserException(sprintf('Unable to parse %s', $money));
    }
}
