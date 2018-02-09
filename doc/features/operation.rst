Operation
=========

.. ATTENTION:: Operations with Money objects are always immutable. See :ref:`Immutability`.

.. _addition_subtraction:

Addition & Subtraction
----------------------

Additions can be performed using ``add()``.

.. code-block:: php

    $value1 = Money::EUR(800);       // €8.00
    $value2 = Money::EUR(500);       // €5.00

    $result = $value1->add($value2); // €13.00

``add()`` accepts variadic arguments as well.

.. code-block:: php

    $value1 = Money::EUR(800);                // €8.00
    $value2 = Money::EUR(500);                // €5.00
    $value3 = Money::EUR(600);                // €6.00

    $result = $value1->add($value2, $value3); // €19.00

Subtractions can be performed using ``subtract()``.

.. code-block:: php

    $value1 = Money::EUR(800);            // €8.00
    $value2 = Money::EUR(500);            // €5.00

    $result = $value1->subtract($value2); // €3.00

``subtract()`` accepts variadic arguments as well.

.. code-block:: php

    $value1 = Money::EUR(1400);                    // €14.00
    $value2 = Money::EUR(500);                     // €5.00
    $value3 = Money::EUR(600);                     // €6.00

    $result = $value1->subtract($value2, $value3); // €3.00

.. _multiplication_division:

Multiplication & Division
-------------------------

Multiplications can be performed using ``multiply()``.

.. code-block:: php

    $value = Money::EUR(800);       // €8.00

    $result = $value->multiply(2);  // €16.00

Divisions can be performed using ``divide()``.

.. code-block:: php

    $value = Money::EUR(800);       // €8.00

    $result = $value->divide(2);    // €4.00

.. _modulus:

Modulus
-------

Modulus operations can be performed using ``mod()``.

.. code-block:: php

    $value = Money::EUR(830);        // €8.30
    $divisor = Money::EUR(300);      // €3.00

    $result = $value->mod($divisor); // €2.30

.. _rounding_modes:

Rounding Modes
--------------

A number of rounding modes are available for :ref:`multiplication_division` above.

* ``Money::ROUND_HALF_DOWN``
* ``Money::ROUND_HALF_EVEN``
* ``Money::ROUND_HALF_ODD``
* ``Money::ROUND_HALF_UP``
* ``Money::ROUND_UP``
* ``Money::ROUND_DOWN``
* ``Money::ROUND_HALF_POSITIVE_INFINITY``
* ``Money::ROUND_HALF_NEGATIVE_INFINITY``

.. _absolute:

Absolute Value
--------------

``absolute()`` provides the absolute value of a Money object.

.. code-block:: php

    $value = Money::EUR(-800);       // -€8.00

    $result = $value->absolute();    // €8.00

.. _ratio_of:

Ratio Of
--------

``ratioOf()`` provides the ratio of a Money object in comparison to another Money object.

.. code-block:: php

    $three = Money::EUR(300);        // €3.00
    $six = Money::EUR(600);          // €6.00

    $result = $three->ratioOf($six); // 0.5
    $result = $six->ratioOf($three); // 2
