Bitcoin
=======

Since Money is not ISO currency specific, you can construct a currency object by using the code XBT.
For Bitcoin there is also a formatter and a parser available.

Please see the example below how to use the Bitcoin currency:

.. code-block:: php

    use Money\Currency;
    use Money\Formatter\BitcoinMoneyFormatter;
    use Money\Money;
    use Money\Parser\BitcoinMoneyParser;

    // construct bitcoin
    $money = new Money(100000, new Currency('XBT'));

    // format bitcoin
    $formatter = new BitcoinMoneyFormatter(2);
    echo $formatter->format($money); // prints Ƀ1000.00

    // parse bitcoin
    $parser = new BitcoinMoneyParser($intlParser, 2);
    $money = $parser->parse("\0xC9\0x831000.00", 'USD');
    echo $money->getAmount(); // outputs 100000


In most cases you probably don't know the exact currency you are going to format or parse.
For such scenarios, we have an aggregate formatter and a parser which lets you configure multiple parsers
and then choose the best based on the value. See more in :doc:`Formatting` and :doc:`Parsing` section.
