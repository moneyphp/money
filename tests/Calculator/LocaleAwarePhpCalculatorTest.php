<?php

namespace Tests\Money\Calculator;

use Money\Calculator\PhpCalculator;

final class LocaleAwarePhpCalculatorTest extends CalculatorTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setLocale(LC_ALL, 'ru_RU.UTF-8');
    }

    protected function getCalculator()
    {
        return new PhpCalculator();
    }
}
