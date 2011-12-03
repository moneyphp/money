Currency Conversion
===================

To convert a Money instance from one Currency to another, you need a CurrencyPair.
You can use the OOP notation to define a pair:

.. code:: php
   
   <?php
   $pair = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);

You can also parse ISO notations. For example, the quotation ``EUR/USD 1.2500`` 
means that one euro is exchanged for 1.2500 US dollars.

.. code:: php
   
   <?php
   $pair = CurrencyPair::createFromIso('EUR/USD 1.2500');

That should make it easy to work with external sources of conversion rates. (Note 
that if you build integrations with such services, we'll happily take your pull requests!)

After you have the pair, it's dead simple:

.. code:: php
   
   <?php
   $eur100 = Money::EUR(100);
   $usd125 = $pair->convert($eur100);

