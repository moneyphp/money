<?php

namespace Money\Currencies;

/**
 * Currency specification.
 *
 * @author Frederik Bosch
 */
final class Specification
{
    /**
     * @var string
     */
    private $alphabeticCode;
    /**
     * @var int
     */
    private $numericCode;
    /**
     * @var int
     */
    private $subunit;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $entity;

    /**
     * @param $alphabeticCode
     * @param $subunit
     */
    public function __construct($alphabeticCode, $subunit)
    {
        $this->alphabeticCode = $alphabeticCode;
        $this->subunit = $subunit;
    }

    /**
     * @param string $name
     *
     * @return Specification
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
     * @return Specification
     */
    public function withEntity($entity)
    {
        $clone = clone $this;
        $clone->entity = $entity;

        return $clone;
    }

    /**
     * @param int $numericCode
     *
     * @return Specification
     */
    public function withNumericCode($numericCode)
    {
        $clone = clone $this;
        $clone->numericCode = $numericCode;

        return $clone;
    }

    /**
     * @return string
     */
    public function getAlphabeticCode()
    {
        return $this->alphabeticCode;
    }

    /**
     * @return int
     */
    public function getNumericCode()
    {
        return $this->numericCode;
    }

    /**
     * @return int
     */
    public function getSubunit()
    {
        return $this->subunit;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
