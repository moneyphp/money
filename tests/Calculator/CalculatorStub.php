<?php

namespace Tests\Money\Calculator;

use Money\Calculator;

final class CalculatorStub implements Calculator
{
    /**
     * {@inheritdoc}
     */
    public static function supported()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function compare($a, $b)
    {
        // TODO: Implement compare() method.
    }

    /**
     * {@inheritdoc}
     */
    public function add($amount, $addend)
    {
        // TODO: Implement add() method.
    }

    /**
     * {@inheritdoc}
     */
    public function subtract($amount, $subtrahend)
    {
        // TODO: Implement subtract() method.
    }

    /**
     * {@inheritdoc}
     */
    public function multiply($amount, $multiplier)
    {
        // TODO: Implement multiply() method.
    }

    /**
     * {@inheritdoc}
     */
    public function divide($amount, $divisor)
    {
        // TODO: Implement divide() method.
    }

    /**
     * {@inheritdoc}
     */
    public function ceil($number)
    {
        // TODO: Implement ceil() method.
    }

    /**
     * {@inheritdoc}
     */
    public function floor($number)
    {
        // TODO: Implement floor() method.
    }

    /**
     * {@inheritdoc}
     */
    public function absolute($number)
    {
        // TODO: Implement absolute() method.
    }

    /**
     * {@inheritdoc}
     */
    public function round($number, $roundingMode)
    {
        // TODO: Implement round() method.
    }

    /**
     * {@inheritdoc}
     */
    public function share($amount, $ratio, $total)
    {
        // TODO: Implement share() method.
    }

    /**
     * {@inheritdoc}
     */
    public function mod($amount, $divisor)
    {
        // TODO: Implement mod() method.
    }
}
