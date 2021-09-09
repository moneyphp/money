<?php

declare(strict_types=1);

use Money\PHPUnit\Comparator;
use SebastianBergmann\Comparator\Factory;

Factory::getInstance()->register(new Comparator());
