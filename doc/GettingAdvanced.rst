Getting Advanced
================

JSON Output
-----------

If you want to serialize a money object into a JSON, you can just use the PHP method ``json_encode`` for that.
Please find below example of how to achieve this.

.. code-block:: php

    use Money\Money;

    $money = Money::USD(350);
    $json = json_encode($money);
    echo $json; // outputs '{"amount":"350","currency":"USD"}'

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

.. _bitcoin:

Bitcoin
-------

Since Money is not ISO currency specific, you can construct a currency object by using the code XBT.
For Bitcoin there is also a formatter and a parser available. The subunit is 8 for a Bitcoin.

Please see the example below how to use the Bitcoin currency:

.. code-block:: php

    use Money\Currencies\BitcoinCurrencies;
    use Money\Currency;
    use Money\Formatter\BitcoinMoneyFormatter;
    use Money\Money;
    use Money\Parser\BitcoinMoneyParser;

    // construct bitcoin (subunit of 8)
    $money = new Money(100000000000, new Currency('XBT'));

    // construct bitcoin currencies
    $currencies = new BitcoinCurrencies();

    // format bitcoin
    $formatter = new BitcoinMoneyFormatter(2, $currencies);
    echo $formatter->format($money); // prints Ƀ1000.00

    // parse bitcoin
    $parser = new BitcoinMoneyParser($intlParser, 2);
    $money = $parser->parse("\0xC9\0x831000.00", 'XBT');
    echo $money->getAmount(); // outputs 100000000000


In most cases you probably don't know the exact currency you are going to format or parse.
For such scenarios, we have an aggregate formatter and a parser which lets you configure multiple parsers
and then choose the best based on the value. See more in :doc:`Formatting` and :doc:`Parsing` section.
