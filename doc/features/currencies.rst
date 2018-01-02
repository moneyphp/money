Currencies
==========

Applications often a certain subset of currencies. Those currencies come from different data sources. Therefore you can
implement the `Currencies` interface. The interface provides a list of available currencies and the subunit for the
currency.

Money comes with the following implementations out of the box:


ISOCurrencies
-------------

As it's name says, the ISO currencies implementation provides all available ISO4217 currencies. It uses the official
ISO 4217 Maintenance Agency as source for the data.


.. code-block:: php

    use Money\Currencies\ISOCurrencies;
    use Money\Currency;

    $currencies = new ISOCurrencies();
    foreach ($currencies as $currency) {
        echo $currency->getCode(); // prints an available currency code within the repository
    }

    $currencies->contains(new Currency('USD')); // returns boolean whether USD is available in this repository
    $currencies->subunitFor(new Currency('USD')); // returns the subunit for the dollar (2)


BitcoinCurrencies
-----------------

The Bitcoin currencies provides a single currency: the Bitcoin. It uses XBT as its code and has a subunit of 8.


.. code-block:: php

    use Money\Currencies\BitcoinCurrencies;
    use Money\Currency;

    $currencies = new BitcoinCurrencies();
    foreach ($currencies as $currency) {
        echo $currency->getCode(); // prints XBT
    }

    $currencies->contains(new Currency('XBT')); // returns boolean whether XBT is available in this repository (true)
    $currencies->contains(new Currency('USD')); // returns boolean whether USD is available in this repository (false)
    $currencies->subunitFor(new Currency('XBT')); // returns the subunit for the Bitcoin (8)

CurrencyList
------------

The CurrencyList class provides a way for a developer to create a custom currency repository.
The class accepts an array of currency code and minor unit pairs. In case of an invalid array an exception is thrown.

.. code-block:: php

    use Money\Currencies\CurrencyList;
    use Money\Currency;

    $currencies = new CurrencyList([
        'MY1' => 2,
    ]);

    foreach ($currencies as $currency) {
        echo $currency->getCode(); // prints MY1
    }

    $currencies->contains(new Currency('MY1')); // returns boolean whether MY1 is available in this repository (true)
    $currencies->contains(new Currency('USD')); // returns boolean whether USD is available in this repository (false)
    $currencies->subunitFor(new Currency('MY1')); // returns the subunit for the currency MY1


Aggregate Currencies
--------------------

This formatter collects multiple currencies.

.. code-block:: php

    use Money\Currency;
    use Money\Currencies\AggregateCurrencies;
    use Money\Currencies\BitcoinCurrencies;
    use Money\Currencies\ISOCurrencies;

    $currencies = new AggregateCurrencies([
        new BitcoinCurrencies(),
        new ISOCurrencies()
    ]);

    foreach ($currencies as $currency) {
        echo $currency->getCode(); // prints XBT or any ISO currency code
    }

    $currencies->contains(new Currency('XBT')); // returns boolean whether XBT is available in this repository (true)
    $currencies->contains(new Currency('USD')); // returns boolean whether USD is available in this repository (false)
    $currencies->subunitFor(new Currency('XBT')); // returns the subunit for the Bitcoin (8)


This is very useful if you want to support multiple currencies data sources.
