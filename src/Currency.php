<?php

declare(strict_types=1);

namespace Money;

use InvalidArgumentException;
use JsonSerializable;

/**
 * Currency Value Object.
 *
 * Holds Currency specific data.
 *
 * @psalm-immutable
 */
final class Currency implements JsonSerializable
{
    /**
     * Currency code.
     */
    private string $code;

    public function __construct(string $code)
    {
        if ($code === '') {
            throw new InvalidArgumentException('Currency code should not be empty string');
        }

        $this->code = $code;
    }

    /**
     * Returns the currency code.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Checks whether this currency is the same as an other.
     */
    public function equals(Currency $other): bool
    {
        return $this->code === $other->code;
    }

    /**
     * Checks whether this currency is available in the passed context.
     */
    public function isAvailableWithin(Currencies $currencies): bool
    {
        return $currencies->contains($this);
    }

    public function __toString(): string
    {
        return $this->code;
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
