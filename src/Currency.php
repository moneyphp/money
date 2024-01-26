<?php

declare(strict_types=1);

namespace Money;

use JsonSerializable;
use Serializable;

use function strtoupper;

/**
 * Currency Value Object.
 *
 * Holds Currency specific data.
 *
 * @psalm-immutable
 */
final class Currency implements JsonSerializable, Serializable
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
        $this->code = strtoupper($code);
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

    public function __toString(): string
    {
        return $this->code;
    }

    public function jsonSerialize(): string
    {
        return $this->code;
    }

    /**
     * @return array<string, string>
     */
    public function __serialize(): array
    {
        return ['code' => $this->code];
    }

    // TODO Correct PSALM?
    public function __unserialize(array $data): void
    {
        $this->code = (string) $data['code'];
    }

    /**
     * @return array<string, string>
     */
    public function serialize(): array
    {
        return $this->__serialize();
    }

    // TODO Correct PSALM?
    // Correct CODE?
    public function unserialize(string $data): void
    {
        $this->__unserialize((array) $data);
    }
}
