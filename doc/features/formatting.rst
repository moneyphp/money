.. _formatting:

Formatting
==========

It is often necessary that you display the money value somewhere, probably in a specific format.
This is where formatters help you. You can turn a money object into a human readable string.

Money comes with the following implementations out of the box:


Intl Money Formatter
--------------------

As its name says, this formatter requires the `intl` extension and uses ``NumberFormatter``. In order to provide the
correct subunit for the specific currency, you should also provide the specific currency repository.


.. warning::
    Please be aware that using the `intl` extension can give different results in different environments.


.. code-block:: php

    use Money\Currencies\ISOCurrencies;
    use Money\Currency;
    use Money\Formatter\IntlMoneyFormatter;
    use Money\Money;

    $money = new Money(100, new Currency('USD'));
    $currencies = new ISOCurrencies();

    $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
    $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

    echo $moneyFormatter->format($money); // outputs $1.00


Intl Localized Decimal Formatter
--------------------------------

As its name says, this formatter requires the `intl` extension and uses ``NumberFormatter``. In order to provide the
correct subunit for the specific currency, you should also provide the specific currency repository. This formatter
prints a localized decimal value and therefore does not include a currency sign.


.. warning::
    Please be aware that using the `intl` extension can give different results in different environments.


.. code-block:: php

    use Money\Currencies\ISOCurrencies;
    use Money\Currency;
    use Money\Formatter\IntlLocalizedDecimalFormatter;
    use Money\Money;

    $money = new Money(100000, new Currency('EUR'));
    $currencies = new ISOCurrencies();

    $numberFormatter = new \NumberFormatter('nl_NL', \NumberFormatter::DECIMAL);
    $moneyFormatter = new IntlLocalizedDecimalFormatter($numberFormatter, $currencies);

    echo $moneyFormatter->format($money); // outputs 1.000,00


Decimal Formatter
-----------------

This formatter outputs a simple decimal string which is always in a consistent format independent of locale. In order
to provide the correct subunit for the specific currency, you should provide the specific currency repository.


.. code-block:: php

    use Money\Currencies\ISOCurrencies;
    use Money\Currency;
    use Money\Formatter\DecimalMoneyFormatter;
    use Money\Money;

    $money = new Money(100, new Currency('USD'));
    $currencies = new ISOCurrencies();

    $moneyFormatter = new DecimalMoneyFormatter($currencies);

    echo $moneyFormatter->format($money); // outputs 1.00


Aggregate Formatter
-------------------

This formatter collects multiple formatters and chooses the most appropriate one based on
currency code.

.. code-block:: php

    use Money\Currencies\BitcoinCurrencies;
    use Money\Currencies\ISOCurrencies;
    use Money\Currency;
    use Money\Formatter\AggregateMoneyFormatter;
    use Money\Formatter\BitcoinMoneyFormatter;
    use Money\Formatter\IntlMoneyFormatter;
    use Money\Money;

    $dollars = new Money(100, new Currency('USD'));
    $bitcoin = new Money(100, new Currency('XBT'));

    $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
    $intlFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());
    $bitcoinFormatter = new BitcoinMoneyFormatter(7, new BitcoinCurrencies());

    $moneyFormatter = new AggregateMoneyFormatter([
        'USD' => $intlFormatter,
        'XBT' => $bitcoinFormatter,
    ]);

    echo $moneyFormatter->format($dollars); // outputs $1.00
    echo $moneyFormatter->format($bitcoin); // outputs Ƀ0.0000010


This is very useful if you want to use one formatter as a service in DI context
and want to support multiple currencies.


Bitcoin Formatter
-----------------

See :ref:`Bitcoin <bitcoin>`.
