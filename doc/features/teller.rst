.. _teller:

Teller
======

Legacy codebases often use float math for monetary calculations, which leads to problems with fractions-of-pennies in monetary amounts. The proper solution is to introduce a Money object, and use Money objects in place of float math. However, doing so can be quite an onerous task, especially when the float values need to be moved to and from database storage; intercepting and coercing the float values (often represented by strings) can be very difficult and time-consuming.

To help ease the transition from float math to Money objects, use a Teller instance to replace float math for monetary calculations in place:

.. code-block:: php

    // before
    $price = 234.56;
    $discount = 0.05;
    $discountAmount = $price * $discount; // 11.728

    // after
    $teller = \Money\Teller::USD();
    $discountAmount = $teller->multiply($price, $discount); // '11.73'

The main drawback is that you cannot use two different currencies with the Teller; you can use only one.

The Teller offers these methods:

* operation

    * ``absolute($amount) : string`` Returns an absolute monetary amount.
    * ``add($amount, $other, ...$others) : string`` Adds one or more monetary amounts to a monetary amount.
    * ``divide($amount, $divisor) : string`` Divides a monetary amount by a divisor.
    * ``mod($amount, $divisor)`` Returns the mod of one amount by another.
    * ``multiply($amount, $multiplier) : string`` Multiplies a monetary amount by a multiplier.
    * ``negative($amount) : string`` Negates a monetary amount.
    * ``ratioOf($amount, $other)`` Determines the ratio of one monetary amount to another.
    * ``subtract($amount, $other, ...$others) : string`` Subtracts one or more monetary amounts from a monetary amount.

* comparison

    * ``compare($amount, $other) : int`` Compares one monetary amount to the other; -1 is less than, 0 is equals, 1 is greater than.
    * ``equals($amount, $other) : bool`` Are two monetary amounts equal?
    * ``greaterThan($amount, $other) : bool`` Is one monetary amount greater than the other?
    * ``greaterThanOrEqual($amount, $other) : bool`` Is one monetary amount greater than or equal to the other?
    * ``isNegative($amount) : bool`` Is a monetary amount less than zero?
    * ``isPositive($amount) : bool`` Is a monetary amount greater than zero?
    * ``isZero($amount) : bool`` Is a monetary amount equal to zero?
    * ``lessThan($amount, $other) : bool`` Is one monetary amount less than the other?
    * ``lessThanOrEqual($amount, $other) : bool`` Is one monetary amount less than or equal to the other?

* allocation

    * ``allocate($amount, array $ratios) : string[]`` Allocates a monetary amount according to an array of ratios.
    * ``allocateTo($amount, $n) : string[]`` Allocates a monetary amount among N targets.

* aggregation

    * ``avg($amount, ...$amounts) : string`` Averages a series of monetary amounts.
    * ``sum($amount, ...$amounts) : string`` Sums a series of monetary amounts.
    * ``max($amount, ...$amounts) : string`` Finds the highest of a series of monetary amounts.
    * ``min($amount, ...$amounts) : string`` Finds the lowest of a series of monetary amounts.

* conversion

    * ``convertToMoney($amount) : Money`` Converts a monetary amount to a Money object.
    * ``convertToMoneyArray(array $amounts) : Money`` Converts an array of monetary amounts to an array of Money objects.
    * ``convertToString($amount) : string`` Converts a monetary amount to a string.
    * ``convertToStringArray($amount) : string`` Converts an array of monetary amounts to an array of strings.
    * ``zero() : string`` Returns a zero monetary amount (``'0.00'``).
