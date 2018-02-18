Currency Conversion
===================

To convert a Money instance from one Currency to another, you need the Converter. This class depends on
Currencies and Exchange. Exchange returns a `CurrencyPair`, which is the combination of the base
currency, counter currency and the conversion ratio.


Fixed Exchange
--------------

You can use a fixed exchange to convert `Money` into another Currency.

.. code:: php

    use Money\Converter;
    use Money\Currency;
    use Money\Exchange\FixedExchange;

    $exchange = new FixedExchange([
        'EUR' => [
            'USD' => 1.25
        ]
    ]);

    $converter = new Converter(new ISOCurrencies(), $exchange);

    $eur100 = Money::EUR(100);
    $usd125 = $converter->convert($eur100, new Currency('USD'));


Reversed Currencies Exchange
----------------------------

In some cases you might want the Exchange to resolve the reverse of the Currency Pair
as well if the original cannot be found. To add this behaviour to any Exchange
you need to wrap it in in a `ReversedCurrenciesExchange`. If a reverse Currency Pair
can be found, it's simply used as a divisor of 1 to calculate the reverse
conversion ratio.

For example this can be useful if you use a `FixedExchange` and you don't want to
define the currency pairs in both directions.

.. code:: php

    use Money\Converter;
    use Money\Currency;
    use Money\Exchange\FixedExchange;
    use Money\Exchange\ReversedCurrenciesExchange;

    $exchange = new ReversedCurrenciesExchange(new FixedExchange([
        'EUR' => [
            'USD' => 1.25
        ]
    ]));

    $converter = new Converter(new ISOCurrencies(), $exchange);

    $usd125 = Money::USD(125);
    $eur100 = $converter->convert($usd125, new Currency('EUR'));


Third Party Integrations
------------------------

We also provide a way to integrate external sources of conversion rates by implementing
the ``Money\Exchange`` interface.


Swap
^^^^

Swap_ is a currency exchanger library widespread in the PHP ecosystem. You can install it via Composer_:

.. code:: bash

    $ composer require florianv/swap


Then conversion is quite simple:

.. code:: php

    use Money\Money;
    use Money\Converter;
    use Money\Exchange\SwapExchange;

    // $swap = Implementation of \Swap\SwapInterface
    $exchange = new SwapExchange($swap);

    $converter = new Converter(new ISOCurrencies(), $exchange);
    $eur100 = Money::EUR(100);
    $usd125 = $converter->convert($eur100, new Currency('USD'));


.. _Swap: https://github.com/florianv/swap
.. _Composer: https://getcomposer.org


Exchanger
^^^^^^^^^

Exchanger_ is the currency exchange framework behind Swap_.

.. code:: bash

    $ composer require florianv/exchanger


Then conversion is quite simple:

.. code:: php

    use Money\Money;
    use Money\Converter;
    use Money\Exchanger\ExchangerExchange;

    // $exchanger = Implementation of \Exchanger\Contract\ExchangeRateProvider
    $exchange = new ExchangerExchange($exchanger);

    $converter = new Converter(new ISOCurrencies(), $exchange);
    $eur100 = Money::EUR(100);
    $usd125 = $converter->convert($eur100, new Currency('USD'));


.. _Exchanger: https://github.com/florianv/exchanger


CurrencyPair
------------

A CurrencyPair is returned by the Exchange. If you want to implement your own Exchange, you can use
the OOP notation to define a pair:

.. code:: php

    use Money\Currency;
    use Money\CurrencyPair;

    $pair = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);


But you can also parse ISO notations. For example, the quotation ``EUR/USD 1.2500``
means that one euro is exchanged for 1.2500 US dollars.

.. code:: php

    use Money\CurrencyPair;

    $pair = CurrencyPair::createFromIso('EUR/USD 1.2500');

You could also create a pair using a third party. There is a default one in the core using Swap_
which you can install via Composer_.

.. code:: php

    use Money\Currency;
    use Money\Exchange\SwapExchange;

    $eur = new Currency('EUR');
    $usd = new Currency('USD');

    // $swap = Implementation of \Swap\SwapInterface
    $exchange = new SwapExchange($swap);

    $pair = $exchange->quote($eur, $usd);
