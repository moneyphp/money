
Immutability
============

Jim and Hannah both want to buy a copy of book priced at EUR 25. 

.. code-block:: php
   
   <?php
   $jim_price = $hannah_price = Money::EUR(2500);

Jim has a coupon for EUR 5.
   
.. code-block:: php
   
   <?php
   $coupon = Money::EUR(500);
   $jim_price->subtract($coupon);

Because ``$jim_price`` and ``$hannah_price`` are the same object, you'd expect Hannah to now have the reduced
price as well. To prevent this problem, Money objects are **immutable**. With the code above, both 
``$jim_price`` and ``$hannah_price`` are still EUR 25:

.. code-block:: php
   
   <?php 
   $jim_price->equals($hannah_price); // true

The correct way of doing operations is:

.. code-block:: php
   
   <?php
   $jim_price = $jim_price->subtract($coupon);
   $jim_price->lessThan($hannah_price); // true
   $jim_price->equals(Money::EUR(2000)); // true
   

