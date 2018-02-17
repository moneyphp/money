<?php

namespace Money\Calculator;

use Money\Calculator;

/**
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @internal
 */
final class Registry
{
    /**
     * @var Calculator
     */
    private $calculator;

    /**
     * @var array
     */
    private $calculators = [
        BcMathCalculator::class => true,
        GmpCalculator::class => true,
        PhpCalculator::class => true,
    ];

    /**
     * @var Registry
     */
    private static $instance;

    /**
     * @param string $calculator
     */
    public function registerCalculator($calculator)
    {
        if (is_a($calculator, Calculator::class, true) === false) {
            throw new \InvalidArgumentException('Calculator must implement '.Calculator::class);
        }

        // Calculator already registered
        if (isset($this->calculators[$calculator])) {
            return;
        }

        $this->calculators = array_reverse($this->calculators, true);
        $this->calculators[$calculator] = true;
        $this->calculators = array_reverse($this->calculators, true);

        $this->resetCalculator();
    }

    /**
     * Enables a calculator.
     *
     * @param string $calculator
     */
    public function enableCalculator($calculator)
    {
        if (!isset($this->calculators[$calculator])) {
            throw new \RuntimeException(sprintf('Calculator %s must be registered first', $calculator));
        }

        $this->calculators[$calculator] = true;

        $this->resetCalculator();
    }

    /**
     * Disables a calculator.
     *
     * @param string $calculator
     */
    public function disableCalculator($calculator)
    {
        if (!isset($this->calculators[$calculator])) {
            throw new \RuntimeException(sprintf('Calculator %s must be registered first', $calculator));
        }

        $this->calculators[$calculator] = false;

        $this->resetCalculator();
    }

    /**
     * Disables arbitrary precision calculators.
     */
    public function disableArbitraryPrecisionCalculators()
    {
        $this->disableCalculator(BcMathCalculator::class);
        $this->disableCalculator(GmpCalculator::class);
    }

    /**
     * Resets the already resolved calculator instance.
     * Used when the registry is modified in any way (eg. registering a new calculator).
     */
    private function resetCalculator()
    {
        $this->calculator = null;
    }

    /**
     * @return Calculator
     *
     * @throws \RuntimeException If cannot find calculator for money calculations
     */
    private function initializeCalculator()
    {
        $calculators = $this->calculators;

        foreach ($calculators as $calculator => $enabled) {
            /** @var Calculator $calculator */
            if ($enabled && $calculator::supported()) {
                return new $calculator();
            }
        }

        throw new \RuntimeException('Cannot find calculator for money calculations');
    }

    /**
     * @return Calculator
     */
    public function getCalculator()
    {
        if (null === $this->calculator) {
            $this->calculator = $this->initializeCalculator();
        }

        return $this->calculator;
    }

    /**
     * Yep, singleton. It ensures that all Money components use the same calculator context.
     *
     * @return self
     */
    public static function instance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
