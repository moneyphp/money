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
    private static $calculator;

    /**
     * @var array
     */
    private static $calculators = [
        BcMathCalculator::class,
        GmpCalculator::class,
        PhpCalculator::class,
    ];

    /**
     * @param string $calculator
     */
    public static function registerCalculator($calculator)
    {
        if (is_a($calculator, Calculator::class, true) === false) {
            throw new \InvalidArgumentException('Calculator must implement '.Calculator::class);
        }

        array_unshift(self::$calculators, $calculator);
    }

    /**
     * @return Calculator
     *
     * @throws \RuntimeException If cannot find calculator for money calculations
     */
    private static function initializeCalculator()
    {
        $calculators = self::$calculators;

        foreach ($calculators as $calculator) {
            /** @var Calculator $calculator */
            if ($calculator::supported()) {
                return new $calculator();
            }
        }

        throw new \RuntimeException('Cannot find calculator for money calculations');
    }

    /**
     * @return Calculator
     */
    public static function getCalculator()
    {
        if (null === static::$calculator) {
            static::$calculator = static::initializeCalculator();
        }

        return static::$calculator;
    }
}
