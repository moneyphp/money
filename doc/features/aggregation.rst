.. _aggregation:

Aggregation
===========

``min()`` returns the smallest of the given Money objects

.. code-block:: php

    $first = Money::EUR(100);                  // €1.00
    $second = Money::EUR(200);                 // €2.00
    $third = Money::EUR(300);                  // €3.00

    $min = Money::min($first, $second, $third) // €1.00

``max()`` returns the largest of the given Money objects

.. code-block:: php

    $first = Money::EUR(100);                  // €1.00
    $second = Money::EUR(200);                 // €2.00
    $third = Money::EUR(300);                  // €3.00

    $max = Money::max($first, $second, $third) // €3.00

``avg()`` returns the average value of the given Money objects as a Money object

.. code-block:: php

    $first = Money::EUR(100);                  // €1.00
    $second = Money::EUR(-200);                // -€2.00
    $third = Money::EUR(300);                  // €3.00

    $avg = Money::avg($first, $second, $third) // €2.00

``sum()`` provides the sum of all given Money objects

.. code-block:: php

    $first = Money::EUR(100);                  // €1.00
    $second = Money::EUR(-200);                // -€2.00
    $third = Money::EUR(300);                  // €3.00

    $sum = Money::sum($first, $second, $third) // €2.00
