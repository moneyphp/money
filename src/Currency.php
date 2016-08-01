<?php

namespace Money;

/**
 * Currency Value Object.
 *
 * Holds Currency specific data.
 *
 * @author Mathias Verraes
 */
final class Currency implements \JsonSerializable
{
    /**
     * Currency code.
     *
     * @var string
     */
    private $code;
    /**
     * @var int
     */
    private $subunit = 0;
    /**
     * @var string
     */
    private $name = '';
    /**
     * @var string
     */
    private $entity = '';

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
     * @param int $subunit
     *
     * @return Currency
     */
    public function withSubunit($subunit)
    {
        if (!is_int($subunit)) {
            throw new \InvalidArgumentException('Subunit should be an integer');
        }

        $clone = clone $this;
        $clone->subunit = $subunit;

        return $clone;
    }

    /**
     * @param string $name
     *
     * @return Currency
     */
    public function withName($name)
    {
        $clone = clone $this;
        $clone->name = $name;

        return $clone;
    }

    /**
     * @param string $entity
     *
     * @return Currency
     */
    public function withEntity($entity)
    {
        $clone = clone $this;
        $clone->entity = $entity;

        return $clone;
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
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSubunit()
    {
        return $this->subunit;
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
     * @param Currencies $currencies
     *
     * @return bool
     */
    public function isAvailableWithin(Currencies $currencies)
    {
        return $currencies->contains($this);
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
