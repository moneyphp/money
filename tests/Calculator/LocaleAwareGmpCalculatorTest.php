<?php

namespace Tests\Money\Calculator;

final class LocaleAwareGmpCalculatorTest extends GmpCalculatorTest
{
    public function setUp()
    {
        parent::setUp();

        $this->setLocale(LC_ALL, 'ru_RU.UTF-8');
    }
}
