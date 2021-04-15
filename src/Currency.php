<?php

declare(strict_types=1);

namespace Money;

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
     *
     * @psalm-var non-empty-string
     */
    private string $code;

    /** @psalm-param non-empty-string $code */
    public function __construct(string $code)
    {
        $this->code = $code;
    }

    /**
     * Returns the currency code.
     *
     * @psalm-return non-empty-string
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
     *
     * @deprecated please use {@see Currencies::contains()} instead
     *
     * @psalm-suppress ImpureMethodCall this method uses an external potentially side-effect-inducing API
     * @TODO should we just drop this?
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
