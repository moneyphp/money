<?php

declare(strict_types=1);

namespace Money;

use JsonSerializable;

use function strtoupper;

/**
 * Currency Value Object.
 *
 * Holds Currency specific data.
 *
 * @phpstan-immutable
 */
final class Currency implements JsonSerializable
{
    /**
     * Currency code.
     *
     * @phpstan-var non-empty-string
     */
    private string $code;

    /**
     * @phpstan-param non-empty-string $code
     *
     * @phpstan-pure
     */
    public function __construct(string $code)
    {
        $this->code = strtoupper($code);
    }

    /**
     * Returns the currency code.
     *
     * @phpstan-return non-empty-string
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
     * Returns a new Money with zero amount in this currency.
     */
    public function zero(): Money
    {
        return new Money(0, $this);
    }

    public function __toString(): string
    {
        return $this->code;
    }

    public function jsonSerialize(): string
    {
        return $this->code;
    }
}
