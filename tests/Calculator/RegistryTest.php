<?php

namespace Tests\Money\Calculator;

use Money\Calculator\BcMathCalculator;
use Money\Calculator\GmpCalculator;
use Money\Calculator\PhpCalculator;
use Money\Calculator\Registry;
use PHPUnit\Framework\TestCase;

final class RegistryTest extends TestCase
{
    /**
     * @test
     */
    public function it_registers_calculators()
    {
        $registry = new Registry();

        $registry->registerCalculator(CalculatorStub::class);

        $this->assertInstanceOf(CalculatorStub::class, $registry->getCalculator());
    }

    /**
     * @test
     */
    public function it_disables_calculators()
    {
        $registry = new Registry();

        $registry->disableCalculator(BcMathCalculator::class);
        $registry->disableCalculator(GmpCalculator::class);

        $this->assertInstanceOf(PhpCalculator::class, $registry->getCalculator());
    }

    /**
     * @test
     */
    public function it_disables_arbitrary_precision_calculators()
    {
        $registry = new Registry();

        $registry->disableArbitraryPrecisionCalculators();

        $this->assertInstanceOf(PhpCalculator::class, $registry->getCalculator());
    }

    /**
     * @test
     */
    public function it_enables_calculators()
    {
        $registry = new Registry();

        $registry->registerCalculator(CalculatorStub::class);

        $registry->disableCalculator(CalculatorStub::class);
        $registry->enableCalculator(CalculatorStub::class);

        $this->assertInstanceOf(CalculatorStub::class, $registry->getCalculator());
    }
}
