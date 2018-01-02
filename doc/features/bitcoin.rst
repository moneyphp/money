.. _bitcoin:

Bitcoin
=======

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
    $parser = new BitcoinMoneyParser(2);
    $money = $parser->parse("Ƀ1000.00", 'XBT');
    echo $money->getAmount(); // outputs 100000000000


In most cases you probably don't know the exact currency you are going to format or parse.
For such scenarios, we have an aggregate formatter and a parser which lets you configure multiple parsers
and then choose the best based on the value. See more in :ref:`Formatting <formatting>` and :ref:`Parsing <parsing>` section.
