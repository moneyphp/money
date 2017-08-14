Comparison
==========

A number of built in methods are available for comparing Money objects.

.. _same_currency:

Same Currency
-------------

``isSameCurrency()`` compares whether two Money objects have the same currency.

.. code-block:: php

    $value1 = Money::USD(800);                      // $8.00
    $value2 = Money::USD(100);                      // $1.00
    $value3 = Money::EUR(800);                      // €8.00

    $result = $value1->isSameCurrency($value2);    // true
    $result = $value1->isSameCurrency($value3);    // false

.. _equality:

Equality
--------

``equals()`` compares whether two Money objects are equal in value and currency.

.. code-block:: php

    $value1 = Money::USD(800);              // $8.00
    $value2 = Money::USD(800);              // $8.00
    $value3 = Money::EUR(800);              // €8.00

    $result = $value1->equals($value2);     // true
    $result = $value1->equals($value3);     // false

.. _greater_than:

Greater Than
------------

``greaterThan()`` compares whether the first Money object is larger than the second.

.. code-block:: php

    $value1 = Money::USD(800);                  // $8.00
    $value2 = Money::USD(700);                  // $7.00

    $result = $value1->greaterThan($value2);    // true

You can also use ``greaterThanOrEqual()`` to additionally check for equality.

.. code-block:: php

    $value1 = Money::USD(800);                          // $8.00
    $value2 = Money::USD(800);                          // $8.00

    $result = $value1->greaterThanOrEqual($value2);     // true

.. _less_than:

Less Than
---------

``lessThan()`` compares whether the first Money object is less than the second.

.. code-block:: php

    $value1 = Money::USD(800);              // $8.00
    $value2 = Money::USD(700);              // $7.00

    $result = $value1->lessThan($value2);   // false

You can also use ``lessThanOrEqual()`` to additionally check for equality.

.. code-block:: php

    $value1 = Money::USD(800);                      // $8.00
    $value2 = Money::USD(800);                      // $8.00

    $result = $value1->lessThanOrEqual($value2);    // true

.. _value_sign:

Value Sign
----------

You may determine the sign of Money object using the following methods.

* ``isZero()``
* ``isPositive()``
* ``isNegative()``

.. code-block:: php

    Money::USD(100)->isZero();          // false
    Money::USD(0)->isZero();            // true
    Money::USD(-100)->isZero();         // false

    Money::USD(100)->isPositive();      // true
    Money::USD(0)->isPositive();        // false
    Money::USD(-100)->isPositive();     // false

    Money::USD(100)->isNegative();      // false
    Money::USD(0)->isNegative();        // false
    Money::USD(-100)->isNegative();     // true
