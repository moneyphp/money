<?php

declare(strict_types=1);

namespace Money\Exchange;

use InvalidArgumentException;
use Money\Calculator;
use Money\Calculator\BcMathCalculator;
use Money\Calculator\GmpCalculator;
use Money\Calculator\PhpCalculator;
use Money\Currencies;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use RuntimeException;
use SplQueue;
use stdClass;

use function array_reduce;
use function array_reverse;
use function array_unshift;
use function assert;
use function is_a;

/**
 * Provides a way to get an exchange rate through a minimal set of intermediate conversions.
 */
final class IndirectExchange implements Exchange
{
    private static ?Calculator $calculator = null;

    /** @psalm-var non-empty-list<class-string<Calculator>> */
    private static array $calculators = [
        BcMathCalculator::class,
        GmpCalculator::class,
        PhpCalculator::class,
    ];

    private Currencies $currencies;

    private Exchange $exchange;

    public function __construct(Exchange $exchange, Currencies $currencies)
    {
        $this->exchange   = $exchange;
        $this->currencies = $currencies;
    }

    /** @psalm-param class-string<Calculator> $calculator */
    public static function registerCalculator(string $calculator): void
    {
        if (is_a($calculator, Calculator::class, true) === false) {
            throw new InvalidArgumentException('Calculator must implement ' . Calculator::class);
        }

        array_unshift(self::$calculators, $calculator);
    }

    public function quote(Currency $baseCurrency, Currency $counterCurrency): CurrencyPair
    {
        try {
            return $this->exchange->quote($baseCurrency, $counterCurrency);
        } catch (UnresolvableCurrencyPairException) {
            $rate = array_reduce($this->getConversions($baseCurrency, $counterCurrency), function ($carry, CurrencyPair $pair) {
                return $this->getCalculator()->multiply($carry, $pair->getConversionRatio());
            }, '1.0');

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
        $startNode             = $this->initializeNode($baseCurrency);
        $startNode->discovered = true;

        $nodes = [$baseCurrency->getCode() => $startNode];

        $frontier = new SplQueue();
        $frontier->enqueue($startNode);

        while ($frontier->count()) {
            $currentNode = $frontier->dequeue();
            assert($currentNode instanceof stdClass);

            $currentCurrency = $currentNode->currency;
            assert($currentCurrency instanceof Currency);

            if ($currentCurrency->equals($counterCurrency)) {
                return $this->reconstructConversionChain($nodes, $currentNode);
            }

            foreach ($this->currencies as $candidateCurrency) {
                assert($candidateCurrency instanceof Currency);
                if (! isset($nodes[$candidateCurrency->getCode()])) {
                    $nodes[$candidateCurrency->getCode()] = $this->initializeNode($candidateCurrency);
                }

                $node = $nodes[$candidateCurrency->getCode()];
                assert($node instanceof stdClass);

                if ($node->discovered) {
                    continue;
                }

                try {
                    // Check if the candidate is a neighbor. This will throw an exception if it isn't.
                    $this->exchange->quote($currentCurrency, $candidateCurrency);

                    $node->discovered = true;
                    $node->parent     = $currentNode;

                    $frontier->enqueue($node);
                } catch (UnresolvableCurrencyPairException $exception) {
                    // Not a neighbor. Move on.
                }
            }
        }

        throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
    }

    /**
     * @psalm-return object{
     *     currency: Currency,
     *     discovered: false,
     *     parent: null
     * }
     */
    private function initializeNode(Currency $currency): stdClass
    {
        $node = new stdClass();

        $node->currency   = $currency;
        $node->discovered = false;
        $node->parent     = null;

        return $node;
    }

    /**
     * @param stdClass[] $currencies
     * @psalm-param array<non-empty-string, object{
     *     currency: Currency,
     *     discovered: bool,
     *     parent: object|null
     * }> $currencies
     * @psalm-param object{
     *     currency: Currency,
     *     discovered: bool,
     *     parent: object|null
     * } $goalNode
     *
     * @return CurrencyPair[]
     * @psalm-return list<CurrencyPair>
     */
    private function reconstructConversionChain(array $currencies, stdClass $goalNode): array
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

    private function getCalculator(): Calculator
    {
        if (self::$calculator === null) {
            self::$calculator = self::initializeCalculator();
        }

        return self::$calculator;
    }

    /**
     * @throws RuntimeException If cannot find calculator for money calculations.
     */
    private static function initializeCalculator(): Calculator
    {
        foreach (self::$calculators as $calculator) {
            if ($calculator::supported()) {
                return new $calculator();
            }
        }

        throw new RuntimeException('Cannot find calculator for money calculations');
    }
}
