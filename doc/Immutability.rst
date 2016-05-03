Immutability
============

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
