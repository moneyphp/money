Allocation
==========

Allocate by Ratios
------------------

My company made a whopping profit of 5 cents, which has to be divided amongst myself (70%) and my
investor (30%). Cents can't be divided, so I can't give 3.5 and 1.5 cents. If I round up,
I get 4 cents, the investor gets 2, which means I need to conjure up an additional cent. Rounding
down to 3 and 1 cent leaves me 1 cent. Apart from re-investing that cent in the company, the best solution
is to keep handing out the remainder until all money is spent. In other words:

.. code-block:: php

    use Money\Money;

    $profit = Money::EUR(5);
    list($my_cut, $investors_cut) = $profit->allocate([70, 30]);
    // $my_cut is 4 cents, $investors_cut is 1 cent

    // The order is important:
    list($investors_cut, $my_cut) = $profit->allocate([30, 70]);
    // $my_cut is 3 cents, $investors_cut is 2 cents


Allocate to N targets
------------------------

An amount of money can be allocated to N targets using ``allocateTo()``.

.. code-block:: php

    $value = Money::EUR(800);           // $8.00

    $result = $value->allocateTo(3);    // $result = [$2.67, $2.67, $2.66]
