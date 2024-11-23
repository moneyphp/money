<?php

declare(strict_types=1);

namespace Money\Exchange;

use Money\Currencies;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Money\Money;
use SplQueue;

use function array_reduce;
use function array_reverse;

/**
 * Provides a way to get an exchange rate through a minimal set of intermediate conversions.
 */
final class IndirectExchange implements Exchange
{
    public function __construct(private readonly Exchange $exchange, private readonly Currencies $currencies)
    {
    }

    public function quote(Currency $baseCurrency, Currency $counterCurrency): CurrencyPair
    {
        try {
            return $this->exchange->quote($baseCurrency, $counterCurrency);
        } catch (UnresolvableCurrencyPairException) {
            $rate = array_reduce(
                $this->getConversions($baseCurrency, $counterCurrency),
                /**
                 * @phpstan-param numeric-string $carry
                 *
                 * @phpstan-return numeric-string
                 */
                static function (string $carry, CurrencyPair $pair) {
                    $calculator = Money::getCalculator();

                    return $calculator::multiply($carry, $pair->getConversionRatio());
                },
                '1.0'
            );

            return new CurrencyPair($baseCurrency, $counterCurrency, $rate);
        }
    }

    /**
     * @return CurrencyPair[]
     *
     * @throws UnresolvableCurrencyPairException
     */
    private function getConversions(Currency $baseCurrency, Currency $counterCurrency): array
    {
        $startNode             = new IndirectExchangeQueuedItem($baseCurrency);
        $startNode->discovered = true;

        /** @phpstan-var array<non-empty-string, IndirectExchangeQueuedItem> $nodes */
        $nodes = [$baseCurrency->getCode() => $startNode];

        /** @psam-var SplQueue<IndirectExchangeQueuedItem> $frontier */
        $frontier = new SplQueue();
        $frontier->enqueue($startNode);

        while ($frontier->count()) {
            /** @phpstan-var IndirectExchangeQueuedItem $currentNode */
            $currentNode     = $frontier->dequeue();
            $currentCurrency = $currentNode->currency;

            if ($currentCurrency->equals($counterCurrency)) {
                return $this->reconstructConversionChain($nodes, $currentNode);
            }

            foreach ($this->currencies as $candidateCurrency) {
                if (! isset($nodes[$candidateCurrency->getCode()])) {
                    $nodes[$candidateCurrency->getCode()] = new IndirectExchangeQueuedItem($candidateCurrency);
                }

                $node = $nodes[$candidateCurrency->getCode()];

                if ($node->discovered) {
                    continue;
                }

                try {
                    // Check if the candidate is a neighbor. This will throw an exception if it isn't.
                    $this->exchange->quote($currentCurrency, $candidateCurrency);

                    $node->discovered = true;
                    $node->parent     = $currentNode;

                    $frontier->enqueue($node);
                } catch (UnresolvableCurrencyPairException) {
                    // Not a neighbor. Move on.
                }
            }
        }

        throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
    }

    /**
     * @phpstan-param array<non-empty-string, IndirectExchangeQueuedItem> $currencies
     *
     * @return CurrencyPair[]
     * @phpstan-return list<CurrencyPair>
     */
    private function reconstructConversionChain(array $currencies, IndirectExchangeQueuedItem $goalNode): array
    {
        $current     = $goalNode;
        $conversions = [];

        while ($current->parent) {
            $previous      = $currencies[$current->parent->currency->getCode()];
            $conversions[] = $this->exchange->quote($previous->currency, $current->currency);
            $current       = $previous;
        }

        return array_reverse($conversions);
    }
}
