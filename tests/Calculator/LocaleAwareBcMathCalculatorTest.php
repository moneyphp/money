<?php

namespace Tests\Money\Calculator;

final class LocaleAwareBcMathCalculatorTest extends BcMathCalculatorTest
{
    public function setUp()
    {
        parent::setUp();

        $this->setLocale(LC_ALL, 'ru_RU.UTF-8');
    }
}
