<?php
/**
 * This file is part of the Money library
 *
 * Copyright (c) 2011-2014 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money;

use InvalidArgumentException;

class RoundingMode
{
    const ROUND_HALF_UP = PHP_ROUND_HALF_UP;
    const ROUND_HALF_DOWN = PHP_ROUND_HALF_DOWN;
    const ROUND_HALF_EVEN = PHP_ROUND_HALF_EVEN;
    const ROUND_HALF_ODD = PHP_ROUND_HALF_ODD;

    /**
     * @var
     */
    private $roundingMode;

    /**
     * Create a rounding mode
     * @param int $rounding_mode
     * @throws InvalidArgumentException
     */
    public function __construct($rounding_mode)
    {
        if (!in_array(
            $rounding_mode,
            array(self::ROUND_HALF_DOWN, self::ROUND_HALF_EVEN, self::ROUND_HALF_ODD, self::ROUND_HALF_UP)
        )) {
            throw new InvalidArgumentException(
                'Rounding mode should be RoundingMode::ROUND_HALF_DOWN | ' .
                'RoundingMode::ROUND_HALF_EVEN | RoundingMode::ROUND_HALF_ODD | ' .
                'RoundingMode::ROUND_HALF_UP'
            );
        }
        
        $this->roundingMode = $rounding_mode;
    }

    /**
     * @return int
     */
    public function getRoundingMode()
    {
        return $this->roundingMode;
    }

    /**
     * @return RoundingMode
     */
    public static function halfUp()
    {
        return new self(self::ROUND_HALF_UP);
    }

    /**
     * @return RoundingMode
     */
    public static function halfDown()
    {
        return new self(self::ROUND_HALF_DOWN);
    }

    /**
     * @return RoundingMode
     */
    public static function halfEven()
    {
        return new self(self::ROUND_HALF_EVEN);
    }

    /**
     * @return RoundingMode
     */
    public static function halfOdd()
    {
        return new self(self::ROUND_HALF_ODD);
    }
}