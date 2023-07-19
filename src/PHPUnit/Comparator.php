<?php

declare(strict_types=1);

namespace Money\PHPUnit;

use Money\Currencies\AggregateCurrencies;
use Money\Currencies\BitcoinCurrencies;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;
use ReflectionMethod;
use SebastianBergmann\Comparator\ComparisonFailure;

use function assert;
use function method_exists;

/**
 * The comparator is for comparing Money objects in PHPUnit tests.
 *
 * Add this to your bootstrap file:
 *
 * \SebastianBergmann\Comparator\Factory::getInstance()->register(new \Money\PHPUnit\Comparator());
 *
 * @internal do not use within your sources: this comparator is only to be used within the test suite of this library
 *
 * @psalm-suppress PropertyNotSetInConstructor the parent implementation includes factories that cannot be initialized here
 */
final class Comparator extends \SebastianBergmann\Comparator\Comparator
{
    private bool $isComparatorVersion5;

    private IntlMoneyFormatter $formatter;

    public function __construct()
    {
        // PHPUnit 10 + sebastian/comparitor:5 remove the parent class
        // constructor. Call conditionally if detected to keep working on
        // previous versions.
        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }

        // Similarly, comparitor:5 changed the constructor signature of
        // ComparisonFailure. This needs to be detected so the correct version
        // can be used depending on installed tools.
        $cfConstructor              = new ReflectionMethod(ComparisonFailure::class, '__construct');
        $parameterCount             = $cfConstructor->getNumberOfParameters();
        $this->isComparatorVersion5 = $parameterCount === 5;

        $currencies = new AggregateCurrencies([
            new ISOCurrencies(),
            new BitcoinCurrencies(),
        ]);

        $numberFormatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $this->formatter = new IntlMoneyFormatter($numberFormatter, $currencies);
    }

    /** {@inheritDoc} */
    public function accepts($expected, $actual): bool
    {
        return $expected instanceof Money && $actual instanceof Money;
    }

    /** {@inheritDoc} */
    public function assertEquals(
        $expected,
        $actual,
        $delta = 0.0,
        $canonicalize = false,
        $ignoreCase = false
    ): void {
        assert($expected instanceof Money);
        assert($actual instanceof Money);

        if (! $expected->equals($actual)) {
            // Handle signature change in different versions; see notes in
            // constructor.
            if ($this->isComparatorVersion5) {
                throw new ComparisonFailure($expected, $actual, $this->formatter->format($expected), $this->formatter->format($actual), 'Failed asserting that two Money objects are equal.');
            }

            throw new ComparisonFailure($expected, $actual, $this->formatter->format($expected), $this->formatter->format($actual), false, 'Failed asserting that two Money objects are equal.');
        }
    }
}
