Concept
=======

This section introduces the concept and basic features of the library

.. _immutability:

Type Safety
-----------

This library abstracts concepts around `Money`, although with minimal run-time validation.
We attempt to leverage PHP 8's type system as much as possible, but sometimes you will
encounter `string` parameters that are documented as being `numeric-string`: should you
encounter these, it means that the contained value **must** be a `string` that passes
an `assert(is_numeric(<your-string>))` check.

Specifically, be aware that `numeric-string` is used in order to guarantee that large numeric
values (larger than `PHP_INT_MAX` or smaller than `PHP_INT_MIN`), as well as precise fractional
values are not approximated unless requested to do so: cast a `numeric-string` to an `int` or `float`
at your own risk.

It is **strongly advised** that you use a type-checker when interacting with this library.

Compatible type-checkers are:

- https://github.com/vimeo/psalm
- https://github.com/phpstan/phpstan

.. warning::
    If you fail to guarantee type-safety when interacting with this library, especially around
    `numeric-string` passed as parameter to methods of its API, then these values will likely
    be accepted, producing late production crashes. Make sure you run a type-checker!


Immutability
------------

Jim and Hannah both want to buy a copy of book priced at EUR 25.

.. code-block:: php

    use Money\Money;

    $jimPrice = $hannahPrice = Money::EUR(2500);


Jim has a coupon for EUR 5.

.. code-block:: php

    $coupon = Money::EUR(500);
    $jimPrice->subtract($coupon);


Because ``$jimPrice`` and ``$hannahPrice`` are the same object, you'd expect Hannah to now have the reduced
price as well. To prevent this problem, Money objects are **immutable**. With the code above, both
``$jimPrice`` and ``$hannahPrice`` are still EUR 25:

.. code-block:: php

   $jimPrice->equals($hannahPrice); // true


The correct way of doing operations is:

.. code-block:: php

   $jimPrice = $jimPrice->subtract($coupon);
   $jimPrice->lessThan($hannahPrice); // true
   $jimPrice->equals(Money::EUR(2000)); // true


JSON
----

If you want to serialize a money object into a JSON, you can just use the PHP method ``json_encode`` for that.
Please find below example of how to achieve this.

.. code-block:: php

    use Money\Money;

    $money = Money::USD(350);
    $json = json_encode($money);
    echo $json; // outputs '{"amount":"350","currency":"USD"}'
