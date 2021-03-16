<?php

namespace Money;

use InvalidArgumentException;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use Money\MoneyParser;
use Money\Parser\DecimalMoneyParser;

class Teller
{
    /**
     * Convenience factory method for a Teller object.
     *
     * <code>
     * $teller = Teller::USD();
     * </code>
     *
     * @param string $method
     *
     * @param array $arguments
     *
     * @return Money
     *
     */
    public static function __callStatic($method, $arguments)
    {
        $class = get_called_class();
        $currency = new Currency($method);
        $currencies = new ISOCurrencies();
        $parser = new DecimalMoneyParser($currencies);
        $formatter = new DecimalMoneyFormatter($currencies);
        $roundingMode = empty($arguments)
            ? Money::ROUND_HALF_UP
            : array_unshift($arguments);

        return new $class(
            $currency,
            $parser,
            $formatter,
            $roundingMode
        );
    }

    /** @var ISOCurrencies */
    private $currencies;

    /** @var Currency */
    private $currency;

    /** @var MoneyFormatter */
    private $formatter;

    /** @var MoneyParser */
    private $parser;

    /** @var int Rounding mode for multiply/divide */
    private $roundingMode;

    /**
     * Constructor.
     *
     * @param Currency $currency
     * @param MoneyParser $parser
     * @param MoneyFormatter $formatter
     * @param int $roundingMode
     */
    public function __construct(
        Currency $currency,
        MoneyParser $parser,
        MoneyFormatter $formatter,
        $roundingMode = Money::ROUND_HALF_UP
    ) {
        $this->currency = $currency;
        $this->parser = $parser;
        $this->formatter = $formatter;
        $this->roundingMode = $roundingMode;
    }

    /**
     * Are two monetary amounts equal to each other?
     *
     * @param mixed $amount A monetary amount.
     * @param mixed $other Another monetary amount.
     *
     * @return bool
     */
    public function equals($amount, $other)
    {
        return $this->convertToMoney($amount)->equals(
            $this->convertToMoney($other)
        );
    }

    /**
     * Returns an integer less than, equal to, or greater than zero if a
     * monetary amount is respectively less than, equal to, or greater than
     * another.
     *
     * @param mixed $amount A monetary amount.
     * @param mixed $other Another monetary amount.
     *
     * @return int
     */
    public function compare($amount, $other)
    {
        return $this->convertToMoney($amount)->compare(
            $this->convertToMoney($other)
        );
    }

    /**
     * Is one monetary amount greater than another?
     *
     * @param mixed $amount A monetary amount.
     * @param mixed $other Another monetary amount.
     *
     * @return bool
     */
    public function greaterThan($amount, $other)
    {
        return $this->convertToMoney($amount)->greaterThan(
            $this->convertToMoney($other)
        );
    }

    /**
     * Is one monetary amount greater than or equal to another?
     *
     * @param mixed $amount A monetary amount.
     * @param mixed $other Another monetary amount.
     *
     * @return bool
     */
    public function greaterThanOrEqual($amount, $other)
    {
        return $this->convertToMoney($amount)->greaterThanOrEqual(
            $this->convertToMoney($other)
        );
    }

    /**
     * Is one monetary amount less than another?
     *
     * @param mixed $amount A monetary amount.
     * @param mixed $other Another monetary amount.
     *
     * @return bool
     */
    public function lessThan($amount, $other)
    {
        return $this->convertToMoney($amount)->lessThan(
            $this->convertToMoney($other)
        );
    }

    /**
     * Is one monetary amount less than or equal to another?
     *
     * @param mixed $amount A monetary amount.
     * @param mixed $other Another monetary amount.
     *
     * @return bool
     */
    public function lessThanOrEqual($amount, $other)
    {
        return $this->convertToMoney($amount)->lessThanOrEqual(
            $this->convertToMoney($other)
        );
    }

    /**
     * Adds a series of monetary amounts to each other in sequence.
     *
     * @param mixed $amount A monetary amount.
     * @param mixed $other Another monetary amount.
     * @param mixed[] $others Subsequent other monetary amounts.
     *
     * @return string The calculated monetary amount.
     */
    public function add($amount, $other, ...$others)
    {
        return $this->convertToString(
            $this->convertToMoney($amount)->add(
                $this->convertToMoney($other),
                ...$this->convertToMoneyArray($others)
            )
        );
    }

    /**
     * Subtracts a series of monetary amounts from each other in sequence.
     *
     * @param mixed $amount A monetary amount.
     * @param mixed $other Another monetary amount.
     * @param mixed[] $others Subsequent monetary amounts.
     *
     * @return string The calculated monetary amount.
     */
    public function subtract($amount, $other, ...$others)
    {
        return $this->convertToString(
            $this->convertToMoney($amount)->subtract(
                $this->convertToMoney($other),
                ...$this->convertToMoneyArray($others)
            )
        );
    }

    /**
     * Multiplies a monetary amount by a factor.
     *
     * @param mixed $amount A monetary amount.
     * @param int|float|string $multiplier The multiplier.
     *
     * @return string The calculated monetary amount.
     */
    public function multiply($amount, $multiplier)
    {
        return $this->convertToString(
            $this->convertToMoney($amount)->multiply(
                $multiplier, $this->roundingMode
            )
        );
    }

    /**
     * Divides a monetary amount by a divisor.
     *
     * @param mixed $amount A monetary amount.
     * @param int|float|string $divisor The divisor.
     *
     * @return string The calculated monetary amount.
     */
    public function divide($amount, $divisor)
    {
        return $this->convertToString(
            $this->convertToMoney($amount)->divide(
                $divisor, $this->roundingMode
            )
        );
    }

    /**
     * Mods a monetary amount by a divisor.
     *
     * @param mixed $amount A monetary amount.
     * @param int|float|string $divisor The divisor.
     *
     * @return string The calculated monetary amount.
     */
    public function mod($amount, $divisor)
    {
        return $this->convertToString(
            $this->convertToMoney($amount)->mod(
                $this->convertToMoney($divisor)
            )
        );
    }

    /**
     * Allocates a monetary amount according to an array of ratios.
     *
     * @param mixed $amount A monetary amount.
     * @param array $ratios An array of ratios.
     *
     * @return string[] The calculated monetary amounts.
     */
    public function allocate($amount, array $ratios)
    {
        return $this->convertToStringArray(
            $this->convertToMoney($amount)->allocate($ratios)
        );
    }

    /**
     * Allocates a monetary amount among N targets.
     *
     * @param mixed $amount A monetary amount.
     * @param int $n The number of targets.
     *
     * @return string[] The calculated monetary amounts.
     */
    public function allocateTo($amount, $n)
    {
        return $this->convertToStringArray(
            $this->convertToMoney($amount)->allocateTo($n)
        );
    }

    /**
     * Determines the ratio of one monetary amount to another.
     *
     * @param mixed $amount A monetary amount.
     * @param array $other Another monetary amount.
     *
     * @return string The calculated monetary amount.
     */
    public function ratioOf($amount, $other)
    {
        return $this->convertToString(
            $this->convertToMoney($amount)->ratioOf(
                $this->convertToMoney($other)
            )
        );
    }

    /**
     * Returns an absolute monetary amount.
     *
     * @param mixed $amount A monetary amount.
     *
     * @return string The absolute monetary amount.
     */
    public function absolute($amount)
    {
        return $this->convertToString(
            $this->convertToMoney($amount)->absolute()
        );
    }

    /**
     * Returns the negative of an amount; note that this will convert negative
     * amounts to positive ones.
     *
     * @param mixed $amount A monetary amount.
     *
     * @return string The negative monetary amount.
     */
    public function negative($amount)
    {
        return $this->convertToString(
            $this->convertToMoney($amount)->negative()
        );
    }

    /**
     * Is the monetary amount equal to zero?
     *
     * @param mixed $amount The monetary amount.
     *
     * @return bool
     */
    public function isZero($amount)
    {
        return $this->convertToMoney($amount)->isZero();
    }

    /**
     * Is the monetary amount greater than zero?
     *
     * @param mixed $amount The monetary amount.
     *
     * @return bool
     */
    public function isPositive($amount)
    {
        return $this->convertToMoney($amount)->isPositive();
    }

    /**
     * Is the monetary amount less than zero?
     *
     * @param mixed $amount The monetary amount.
     *
     * @return bool
     */
    public function isNegative($amount)
    {
        return $this->convertToMoney($amount)->isNegative();
    }

    /**
     * Returns the lowest of a series of monetary values.
     *
     * @param mixed $amount A monetary amount.
     * @param mixed ...$amounts Additional monetary amounts.
     *
     * @return string
     */
    public function min($amount, ...$amounts)
    {
        return $this->convertToString(
            Money::min(
                $this->convertToMoney($amount),
                ...$this->convertToMoneyArray($amounts)
            )
        );
    }

    /**
     * Returns the highest of a series of monetary values.
     *
     * @param mixed $amount A monetary amount.
     * @param mixed ...$amounts Additional monetary amounts.
     *
     * @return string
     */
    public function max($amount, ...$amounts)
    {
        return $this->convertToString(
            Money::max(
                $this->convertToMoney($amount),
                ...$this->convertToMoneyArray($amounts)
            )
        );
    }

    /**
     * Returns the sum of a series of monetary values.
     *
     * @param mixed $amount A monetary amount.
     * @param mixed ...$amounts Additional monetary amounts.
     *
     * @return string
     */
    public function sum($amount, ...$amounts)
    {
        return $this->convertToString(
            Money::sum(
                $this->convertToMoney($amount),
                ...$this->convertToMoneyArray($amounts)
            )
        );
    }

    /**
     * Returns the average of a series of monetary values.
     *
     * @param mixed $amount A monetary amount.
     * @param mixed ...$amounts Additional monetary amounts.
     *
     * @return string
     */
    public function avg($amount, ...$amounts)
    {
        return $this->convertToString(
            Money::avg(
                $this->convertToMoney($amount),
                ...$this->convertToMoneyArray($amounts)
            )
        );
    }

    /**
     * Converts a monetary amount to a Money object.
     *
     * @param mixed $amount A monetary amount.
     *
     * @return Money
     */
    public function convertToMoney($amount)
    {
        return $this->parser->parse((string) $amount, $this->currency);
    }

    /**
     * Converts an array of monetary amounts to an array of Money objects.
     *
     * @param array $amounts An array of monetary amounts.
     *
     * @return Money[]
     */
    public function convertToMoneyArray(array $amounts)
    {
        foreach ($amounts as $key => $amount) {
            $amounts[$key] = $this->convertToMoney($amount);
        }

        return $amounts;
    }

    /**
     * Converts a monetary amount into a Money object, then into a string.
     *
     * @param mixed $amount Typically a Money object, int, float, or string
     * representing a monetary amount.
     *
     * @return string
     */
    public function convertToString($amount)
    {
        if (! $amount instanceof Money) {
            $amount = $this->convertToMoney($amount);
        }

        return $this->formatter->format($amount);
    }

    /**
     * Converts an array of monetary amounts into Money objects, then into
     * strings.
     *
     * @param mixed $amount Typically a Money object, int, float, or string
     * representing a monetary amount.
     *
     * @return array
     */
    public function convertToStringArray(array $amounts)
    {
        foreach ($amounts as $key => $amount) {
            $amounts[$key] = $this->convertToString($amount);
        }

        return $amounts;
    }

    /**
     * Returns a "zero" monetary amount.
     *
     * @return string
     */
    public function zero()
    {
        return '0.00';
    }
}
