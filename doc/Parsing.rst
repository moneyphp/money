Parsing
=======

In an earlier version of Money there was a ``Money::stringToUnits`` method which parsed strings and created
money objects. When the library started to move away from the ISO-only concept, we realized that
there might be other cases when parsing from string is necessary. This led us creating parsers
and moving the ``stringToUnits`` to ``StringToUnitsParser``.

Money comes with the following implementations out of the box:


Intl Parser
-----------

As it's name says, this formatter requires the `intl` extension and uses ``NumberFormatter``. In order to provide the
correct subunit for the specific currency, you should also provide the specific currency repository.


.. warning::
    Please be aware that using the `intl` extension can give different results in different environments.


.. code-block:: php

    use Money\Currencies\ISOCurrencies;
    use Money\Currency;
    use Money\Parser\IntlMoneyParser;
    use Money\Money;

    $money = new Money(100, new Currency('USD'));
    $currencies = new ISOCurrencies();

    $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
    $moneyParser = new IntlMoneyParser($numberFormatter, $currencies);

    $money = $moneyFormatter->parse('1 USD');

    echo $money->getAmount(); // outputs 100


String to Units Parser
----------------------

This parser contains the logic extracted from ``Money::stringToUnits`` method.


.. code-block:: php

    use Money\Money;
    use Money\Parser\StringToUnitsParser;

    $moneyParser = new StringToUnitsParser();

    $money = $moneyParser->parse('1000', 'USD');

    echo $money->getAmount(); // outputs 100000


Aggregate Parser
----------------

This parser collects multiple parsers and chooses the most appropriate one based on success to parse.
Most parsers throw an exception when the string's format is not supported.

.. code-block:: php

    use Money\Parser\AggregateMoneyParser;
    use Money\Parser\BitcoinMoneyParser;
    use Money\Parser\IntlMoneyParser;

    $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
    $intlParser = new IntlMoneyParser($numberFormatter, 2);
    $bitcoinParser = new BitcoinMoneyParser(2);

    $moneyParser = new AggregateParser([
        $intlParser,
        $bitcoinParser,
    ]);

    $dollars = $moneyParser->parse('1 USD');
    $bitcoin = $moneyParser->parse("\0xC9\0x831.00");


This is very useful if you want to use one parser as a service in DI context.


Bitcoin Parser
--------------

See :doc:`Bitcoin`.
