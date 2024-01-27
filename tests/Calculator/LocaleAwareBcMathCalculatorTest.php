<?php

declare(strict_types=1);

namespace Tests\Money\Calculator;

use function setlocale;

use const LC_ALL;

/** @covers \Money\Calculator\BcMathCalculator */
final class LocaleAwareBcMathCalculatorTest extends BcMathCalculatorTest
{
    public function setUp(): void
    {
        parent::setUp();

        setlocale(LC_ALL, 'ru_RU.UTF-8');
    }
}
