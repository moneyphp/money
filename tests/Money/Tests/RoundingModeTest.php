<?php

namespace Money\Tests;

use Money\RoundingMode;
use PHPUnit_Framework_TestCase;

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
     * @expectedException \Money\InvalidArgumentException
     * @expectedExceptionMessage Rounding mode should be RoundingMode::ROUND_HALF_DOWN | RoundingMode::ROUND_HALF_EVEN | RoundingMode::ROUND_HALF_ODD | RoundingMode::ROUND_HALF_UP
     */
    public function ExceptionCheck()
    {
        new RoundingMode(999);
    }
}