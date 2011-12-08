Money
=====

PHP 5.3+ library to make working with money safer, easier, and fun!

In short: You probably shouldn't represent monetary values by a float. Wherever 
you need to represent money, use this Money value object.

    <?php
	$fiveEur = Money::EUR(500);
	$tenEur = $fiveEur->add($fiveEur);
	
	list($part1, $part2, $part3) = $tenEur->allocate(array(1, 1, 1));
	assert($part1->equals(Money::EUR(334)));
	assert($part2->equals(Money::EUR(333)));
	assert($part3->equals(Money::EUR(333)));

The documentation is available at http://money.readthedocs.org

[![Build Status](https://secure.travis-ci.org/mathiasverraes/money.png)](http://travis-ci.org/mathiasverraes/money)