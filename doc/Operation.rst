Operation
=========

.. ATTENTION:: All operations with Money objects are always immutable. See :ref:`Immutability` below.

.. _addition_subtraction:

Addition & Subtraction
--------------------

Additions can be performed using ``add()``.

.. code-block:: php

    $value1 = Money::EUR(800);         // €8.00
    $value2 = Money::EUR(500);         // €5.00

    $result = $value1->add($value2);   // €13.00

Subtractions can be performed using ``subtract()``.

.. code-block:: php

    $value1 = Money::EUR(800);               // €8.00
    $value2 = Money::EUR(500);               // €5.00

    $result = $value1->subtract($value2);    // €3.00

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

.. _allocation:

Allocation
----------

Allocate by Ratios
^^^^^^^^^^^^^^^^^^

Example: My company made a whopping profit of 5 cents, which has to be divided amongst myself (70%) and my
investor (30%). Cents can't be divided, so I can't give 3.5 and 1.5 cents. If I round up,
I get 4 cents, the investor gets 2, which means I need to conjure up an additional cent. Rounding
down to 3 and 1 cent leaves me 1 cent. Apart from re-investing that cent in the company, the best solution
is to keep handing out the remainder until all money is spent. In other words:

.. code-block:: php

    $profit = Money::EUR(5);

    list($my_cut, $investors_cut) = $profit->allocate(70, 30);
    // $my_cut is 4 cents, $investors_cut is 1 cent

Note that the order of the ratios is important:

.. code-block:: php

    $profit = Money::EUR(5);

    list($investors_cut, $my_cut) = $profit->allocate(30, 70);
    // $my_cut is 3 cents, $investors_cut is 2 cents

Allocate among N targets
^^^^^^^^^^^^^^^^^^^^^^^^

An amount of money can be allocated amount N targets using ``allocateTo()``.

.. code-block:: php

    $value = Money::EUR(800);           // $8.00

    $result = $value->allocateTo(3);    // [$3.00, $3.00, $2.00]

.. _immutability:

Immutability
------------

Jim and Hannah both want to buy a copy of book priced at EUR 25.

.. code-block:: php

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
