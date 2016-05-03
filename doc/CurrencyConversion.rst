Currency Conversion
===================

To convert a Money instance from one Currency to another, you need a CurrencyPair.
You can use the OOP notation to define a pair:

.. code:: php

    use Money\Currency;
    use Money\CurrencyPair;

    $pair = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);


You can also parse ISO notations. For example, the quotation ``EUR/USD 1.2500``
means that one euro is exchanged for 1.2500 US dollars.

.. code:: php

    use Money\CurrencyPair;

    $pair = CurrencyPair::createFromIso('EUR/USD 1.2500');


We also provide a way to integrate external sources of conversion rates by implementing
the ``Money\Exchange`` interface. There is a default one in the core using Swap_
which you can install via Composer_:

.. code:: bash

    $ composer require florianv/swap


Then use it to create a pair:

.. code:: php

    use Money\Currency;
    use Money\Exchange\SwapExchange;

    $eur = new Currency('EUR');
    $usd = new Currency('USD');

    // $swap = Implementation of \Swap\SwapInterface
    $exchange = new SwapExchange($swap);

    $pair = $exchange->getCurrencyPair($eur, $usd);


After having the correct currency pair, conversion is quite simple:

.. code:: php

    use Money\Money;

    $eur100 = Money::EUR(100);
    $usd125 = $pair->convert($eur100);


.. _Swap: https://github.com/florianv/swap
.. _Composer: https://getcomposer.org
