<?php

namespace Money;

use JsonSerializable;

/**
 * Currency Value Object.
 *
 * Holds Currency specific data
 *
 * @author Mathias Verraes
 */
final class Currency implements JsonSerializable
{
    /**
     * Currency code.
     *
     * @var string
     */
    private $code;

    /**
     * @param string $code
     */
    public function __construct($code)
    {
        if (!is_string($code)) {
            throw new \InvalidArgumentException('Currency code should be string');
        }

        $this->code = $code;
    }

    /**
     * Returns the currency code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Checks whether this currency is the same as an other.
     *
     * @param Currency $other
     *
     * @return bool
     */
    public function equals(Currency $other)
    {
        return $this->code === $other->code;
    }

    /**
     * Checks whether this currency is available in the passed context.
     *
     * @param AvailableCurrencies $currencies
     *
     * @return bool
     */
    public function isAvailableWithin(AvailableCurrencies $currencies)
    {
        return $currencies->exists($this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getCode();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->code;
    }
}
