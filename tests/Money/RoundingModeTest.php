<?php

/**
 * This file is part of the Money library.
 *
 * Copyright (c) 2011-2014 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money;

class RoundingModeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function Get()
    {
        $rounding_mode = new RoundingMode(RoundingMode::ROUND_HALF_DOWN);

        $this->assertEquals(RoundingMode::ROUND_HALF_DOWN, $rounding_mode->getRoundingMode());
    }
    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Rounding mode should be RoundingMode::ROUND_HALF_DOWN | RoundingMode::ROUND_HALF_EVEN | RoundingMode::ROUND_HALF_ODD | RoundingMode::ROUND_HALF_UP
     */
    public function ExceptionCheck()
    {
        new RoundingMode(999);
    }
}