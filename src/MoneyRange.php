<?php

namespace Money;

/**
 * Money Range Value Object.
 *
 * @author Jaik Dean <jaik@jaikdean.com>
 */
final class MoneyRange implements \JsonSerializable
{
    use MoneyRangeFactory;

    /**
     * @var Money
     */
    private $start;

    /**
     * @var Money
     */
    private $end;

    /**
     * @param Money $start Start value
     * @param Money $end   End value
     *
     * @throws \InvalidArgumentException If the start and end currencies don't match, or the start value is greater than the end value
     */
    public function __construct(Money $start, Money $end)
    {
        if (!$start->isSameCurrency($end)) {
            throw new \InvalidArgumentException('Currencies must be identical');
        }

        if ($start->greaterThan($end)) {
            throw new \InvalidArgumentException('End value must be equal to or larger than start value');
        }

        $this->start = $start;
        $this->end = $end;
    }

    /**
     * Get the start value of this range.
     *
     * @return Money
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Get the start value of this range.
     *
     * @return Money
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Checks whether the value represented by this object equals to the other.
     *
     * @param MoneyRange $other
     *
     * @return bool
     */
    public function equals(MoneyRange $other)
    {
        return $this->isSameCurrency($other)
            && $this->start->equals($other->start)
            && $this->end->equals($other->end)
        ;
    }

    /**
     * Get the mid point value of this range.
     *
     * @param int $roundingMode
     *
     * @return Money
     */
    public function midPoint($roundingMode = Money::ROUND_HALF_UP)
    {
        return $this->start->add(
            $this->end->subtract($this->start)->divide(2, $roundingMode)
        );
    }

    /**
     * Returns a new MoneyRange instance based on the current one using the new start value.
     *
     * @param Money $start New start value
     *
     * @return MoneyRange
     */
    public function setStart(Money $start)
    {
        return new MoneyRange($start, $this->end);
    }

    /**
     * Returns a new MoneyRange instance based on the current one using the new end value.
     *
     * @param Money $end New end value
     *
     * @return MoneyRange
     */
    public function setEnd(Money $end)
    {
        return new MoneyRange($this->start, $end);
    }

    /**
     * Checks whether a Money or MoneyRange has the same Currency as this.
     *
     * @param Money|MoneyRange $other
     *
     * @return bool
     */
    public function isSameCurrency($other)
    {
        if (!$other instanceof Money && !$other instanceof MoneyRange) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Argument passed must be %s or %s.',
                    Money::class,
                    MoneyRange::class
                )
            );
        }

        return $this->start->getCurrency()->equals($other->getCurrency());
    }

    /**
     * Asserts that a Money or MoneyRange has the same currency as this.
     *
     * @param Money|MoneyRange $other
     *
     * @throws \InvalidArgumentException If $other has a different currency
     */
    private function assertSameCurrency($other)
    {
        if (!$other instanceof Money && !$other instanceof MoneyRange) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Argument passed must be %s or %s.',
                    Money::class,
                    MoneyRange::class
                )
            );
        }

        if (!$this->isSameCurrency($other)) {
            throw new \InvalidArgumentException('Currencies must be identical');
        }
    }

    /**
     * Checks whether the range represented by this object contains a value.
     *
     * @param Money $value
     *
     * @return bool
     */
    public function contains(Money $value)
    {
        return $this->isSameCurrency($value)
            && $this->start->lessThanOrEqual($value)
            && $this->end->greaterThanOrEqual($value);
    }

    /**
     * Checks whether the range represented by this object is greater than the value.
     *
     * @param Money $value
     *
     * @return bool
     */
    public function greaterThan(Money $value)
    {
        return $this->start->greaterThan($value);
    }

    /**
     * Checks whether the range represented by this object is less than the value.
     *
     * @param Money $value
     *
     * @return bool
     */
    public function lessThan(Money $value)
    {
        return $this->end->lessThan($value);
    }

    /**
     * Returns the currency of this object.
     *
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->start->getCurrency();
    }

    /**
     * Returns a new MoneyRange object that represents
     * the multiplied value by the given factor.
     *
     * @param float|int|string $multiplier
     * @param int              $roundingMode
     *
     * @return MoneyRange
     */
    public function multiply($multiplier, $roundingMode = Money::ROUND_HALF_UP)
    {
        $start = $this->start->multiply($multiplier, $roundingMode);
        $end = $this->end->multiply($multiplier, $roundingMode);

        return new MoneyRange($start, $end);
    }

    /**
     * Returns a new MoneyRange object that represents
     * the divided value by the given factor.
     *
     * @param float|int|string $divisor
     * @param int              $roundingMode
     *
     * @return MoneyRange
     */
    public function divide($divisor, $roundingMode = Money::ROUND_HALF_UP)
    {
        $start = $this->start->divide($divisor, $roundingMode);
        $end = $this->end->divide($divisor, $roundingMode);

        return new MoneyRange($start, $end);
    }

    /**
     * @return MoneyRange
     */
    public function absolute()
    {
        $start = $this->start->absolute();
        $end = $this->end->absolute();

        if ($start->greaterThan($end)) {
            return new MoneyRange($end, $start);
        }

        return new MoneyRange($start, $end);
    }

    /**
     * Checks if the value represented by this object contains zero.
     *
     * @return bool
     */
    public function containsZero()
    {
        return $this->contains(new Money(0, $this->start->getCurrency()));
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'start' => $this->start->getAmount(),
            'end' => $this->end->getAmount(),
            'currency' => $this->start->getCurrency(),
        ];
    }
}
