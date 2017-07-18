<?php

namespace Money\Exchange;

use Money\Currencies;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use SplQueue;
use stdClass;

/**
 * Provides a way to get an exchange rate through a minimal set of intermediate conversions.
 *
 * @author Michael Cordingley <Michael.Cordingley@gmail.com>
 */
final class IndirectExchange implements Exchange
{
    /**
     * @var Currencies
     */
    private $currencies;

    /**
     * @var Exchange
     */
    private $exchange;

    /**
     * @param Exchange   $exchange
     * @param Currencies $currencies
     */
    public function __construct(Exchange $exchange, Currencies $currencies)
    {
        $this->exchange = $exchange;
        $this->currencies = $currencies;
    }

    /**
     * {@inheritdoc}
     */
    public function quote(Currency $baseCurrency, Currency $counterCurrency)
    {
        $rate = array_reduce($this->getConversions($baseCurrency, $counterCurrency), function ($carry, CurrencyPair $pair) {
            return $carry * $pair->getConversionRatio();
        }, 1.0);

        return new CurrencyPair($baseCurrency, $counterCurrency, $rate);
    }

    /**
     * @param Currency $baseCurrency
     * @param Currency $counterCurrency
     *
     * @return CurrencyPair[]
     *
     * @throws UnresolvableCurrencyPairException
     */
    private function getConversions(Currency $baseCurrency, Currency $counterCurrency)
    {
        $nodes = $this->initializeNodes();

        $startNode = $nodes[$baseCurrency->getCode()];
        $startNode->discovered = true;

        $frontier = new SplQueue();
        $frontier->enqueue($startNode);

        while ($frontier->count()) {
            /** @var stdClass $currentNode */
            $currentNode = $frontier->dequeue();

            /** @var Currency $currentCurrency */
            $currentCurrency = $currentNode->currency;

            if ($currentCurrency->equals($counterCurrency)) {
                return $this->reconstructConversionChain($nodes, $currentNode);
            }

            /** @var Currency $candidateCurrency */
            foreach ($this->currencies as $candidateCurrency) {
                /** @var stdClass $node */
                $node = $nodes[$candidateCurrency->getCode()];

                if (!$node->discovered) {
                    try {
                        // Check if the candidate is a neighbor. This will thrown an exception if it isn't.
                        $this->exchange->quote($currentCurrency, $candidateCurrency);

                        $node->discovered = true;
                        $node->parent = $currentNode;

                        $frontier->enqueue($node);
                    } catch (UnresolvableCurrencyPairException $exception) {
                        // Not a neighbor. Move on.
                    }
                }
            }
        }

        throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
    }

    /**
     * @return stdClass[]
     */
    private function initializeNodes()
    {
        $currencies = [];

        /** @var Currency $currency */
        foreach ($this->currencies as $currency) {
            $node = new stdClass();

            $node->currency = $currency;
            $node->discovered = false;
            $node->parent = null;

            $currencies[$currency->getCode()] = $node;
        }

        return $currencies;
    }

    /**
     * @param array    $currencies
     * @param stdClass $goalNode
     * @return CurrencyPair[]
     */
    private function reconstructConversionChain(array $currencies, stdClass $goalNode)
    {
        $current = $goalNode;
        $conversions = [];

        while ($current->parent) {
            $previous = $currencies[$current->parent->currency->getCode()];
            $conversions[] = $this->exchange->quote($previous->currency, $current->currency);
            $current = $previous;
        }

        return array_reverse($conversions);
    }
}
