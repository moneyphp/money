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

Accepted integer values
-----------------------
The Money object only supports integer(ish) values on instantiation. The following is (not) supported. When a
non-supported value is passed a `\InvalidArgumentException` will be thrown.

.. code-block:: php

    use Money\Currency;
    use Money\Money;

    // int is accepted
    $fiver = new Money(500, new Currency('USD'));

    // string is accepted if integer
    $fiver = new Money('500', new Currency('USD'));

    // string is accepted if fractional part is zero
    $fiver = new Money('500.00', new Currency('USD'));

    // leading zero's are not accepted
    $fiver = new Money('00500', new Currency('USD'));

    // multiple zero's are not accepted
    $fiver = new Money('000', new Currency('USD'));



Installation
------------

Install the library using composer. Execute the following command in your command line.

.. code-block:: bash

    $ composer require moneyphp/money
