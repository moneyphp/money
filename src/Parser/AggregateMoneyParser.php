<?php

namespace Money\Parser;

use Money\Exception;
use Money\MoneyParser;

/**
 * Parses a string into a Money object using other parsers.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class AggregateMoneyParser implements MoneyParser
{
    /**
     * @var MoneyParser[]
     */
    private $parsers = [];

    /**
     * @param MoneyParser[] $parsers
     */
    public function __construct(array $parsers)
    {
        if (empty($parsers)) {
            throw new \InvalidArgumentException(sprintf('Initialize an empty %s is not possible', self::class));
        }

        foreach ($parsers as $parser) {
            if (false === $parser instanceof MoneyParser) {
                throw new \InvalidArgumentException('All parsers must implement '.MoneyParser::class);
            }

            $this->parsers[] = $parser;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function parse($money, $forceCurrency = null)
    {
        foreach ($this->parsers as $parser) {
            try {
                return $parser->parse($money, $forceCurrency);
            } catch (Exception\ParserException $e) {
            }
        }

        throw new Exception\ParserException(sprintf('Unable to parse %s', $money));
    }
}
