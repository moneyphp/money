Getting started
===============

Instantiation
-------------

All amounts are represented in the smallest unit (eg. cents), so USD 5.00 is written as

.. code-block:: php

    use Money\Currency;
    use Money\Money;

    $fiver = new Money(500, new Currency('USD'));
    // or shorter:
    $fiver = Money::USD(500);

See :doc:`features/parsing` for additional ways to instantiate a Money object from strings.

Installation
------------

Install the library using composer. Execute the following command in your command line.

.. code-block:: bash

    $ composer require moneyphp/money
