Integer Limit
=============

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
