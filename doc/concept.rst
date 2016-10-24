Concept
=======

This section introduces the concept and basic features of the library

.. _immutability:

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


Integer Limit
-------------

Although in real life it is highly unprobable, you might have to deal with money values greater than
the integer limit of your system (``PHP_INT_MAX`` constant represents the maximum integer value).

In order to bypass this limit, we introduced `Calculators`. Based on your environment, Money automatically
picks the best internally and globally. The following implementations are available:

- BC Math (requires `bcmath` extension)
- GMP (requires `gmp` extension)
- Plain integer

Calculators are checked for availability in the order above. If no suitable Calculator is found
Money silently falls back to the integer implementation.

Because of PHP's integer limit, money values are stored as string internally and
``Money::getAmount`` also returns string.

.. code-block:: php

    use Money\Currency;
    use Money\Money;

    $hugeAmount = new Money('12345678901234567890', new Currency('USD'));


.. note::
    Remember, because of the integer limit in PHP, you should inject a string that represents your huge amount.


JSON
----

If you want to serialize a money object into a JSON, you can just use the PHP method ``json_encode`` for that.
Please find below example of how to achieve this.

.. code-block:: php

    use Money\Money;

    $money = Money::USD(350);
    $json = json_encode($money);
    echo $json; // outputs '{"amount":"350","currency":"USD"}'
