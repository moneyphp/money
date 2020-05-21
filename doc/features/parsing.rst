.. _parsing:

Parsing
=======

In an earlier version of Money there was a ``Money::stringToUnits`` method which parsed strings and created
money objects. When the library started to move away from the ISO-only concept, we realized that
there might be other cases when parsing from string is necessary. This led us creating parsers
and moving the ``stringToUnits`` to ``StringToUnitsParser`` (later replaced by ``DecimalMoneyParser``).

Money comes with the following implementations out of the box:


Intl Money Parser
-----------------

As its name says, this parser requires the `intl` extension and uses ``NumberFormatter``. In order to provide the
correct subunit for the specific currency, you should also provide the specific currency repository.


.. warning::
    Please be aware that using the `intl` extension can give different results in different environments.


.. code-block:: php

    use Money\Currencies\ISOCurrencies;
    use Money\Parser\IntlMoneyParser;

    $currencies = new ISOCurrencies();

    $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
    $moneyParser = new IntlMoneyParser($numberFormatter, $currencies);

    $money = $moneyParser->parse('$1.00');

    echo $money->getAmount(); // outputs 100


Intl Localized Decimal Parser
-----------------------------

As its name says, this parser requires the `intl` extension and uses ``NumberFormatter``. In order to provide the
correct subunit for the specific currency, you should also provide the specific currency repository.


.. warning::
    Please be aware that using the `intl` extension can give different results in different environments.


.. code-block:: php

    use Money\Currency;
    use Money\Currencies\ISOCurrencies;
    use Money\Parser\IntlLocalizedDecimalParser;

    $currencies = new ISOCurrencies();

    $numberFormatter = new \NumberFormatter('nl_NL', \NumberFormatter::DECIMAL);
    $moneyParser = new IntlLocalizedDecimalParser($numberFormatter, $currencies);

    $money = $moneyParser->parse('1.000,00', new Currency('EUR'));

    echo $money->getAmount(); // outputs 100000


Decimal Parser
--------------

This parser takes a simple decimal string which is always in a consistent format independent of locale. In order to
provide the correct subunit for the specific currency, you should provide the specific currency repository.


.. code-block:: php

    use Money\Currency;
    use Money\Currencies\ISOCurrencies;
    use Money\Parser\DecimalMoneyParser;

    $currencies = new ISOCurrencies();

    $moneyParser = new DecimalMoneyParser($currencies);

    $money = $moneyParser->parse('1000', new Currency('USD'));

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

    $moneyParser = new AggregateMoneyParser([
        $intlParser,
        $bitcoinParser,
    ]);

    $dollars = $moneyParser->parse('1 USD');
    $bitcoin = $moneyParser->parse("Ƀ1.00");


This is very useful if you want to use one parser as a service in DI context.


Bitcoin Parser
--------------

See :ref:`Bitcoin <bitcoin>`.
