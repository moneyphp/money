Money for PHP
=============

This library intends to provide tools for storing and using monetary values in an easy, yet powerful way.


Why a Money library for PHP?
----------------------------

Also see http://verraes.net/2011/04/fowler-money-pattern-in-php/

This is a PHP implementation of the Money pattern, as described in [Fowler2002]_ :

   A large proportion of the computers in this world manipulate money, so it's always puzzled me
   that money isn't actually a first class data type in any mainstream programming language. The
   lack of a type causes problems, the most obvious surrounding currencies. If all your calculations
   are done in a single currency, this isn't a huge problem, but once you involve multiple currencies
   you want to avoid adding your dollars to your yen without taking the currency differences into
   account. The more subtle problem is with rounding. Monetary calculations are often rounded to the
   smallest currency unit. When you do this it's easy to lose pennies (or your local equivalent)
   because of rounding errors.

.. [Fowler2002] Fowler, M., D. Rice, M. Foemmel, E. Hieatt, R. Mee, and R. Stafford, Patterns of Enterprise Application Architecture, Addison-Wesley, 2002. http://martinfowler.com/books.html#eaa


The goal
--------

Implement a reusable Money class in PHP, using all the best practices and taking care of all the
subtle intricacies of handling money.

.. toctree::
   :hidden:

   Money <self>
   getting-started
   concept
   inspiration

.. toctree::
   :hidden:
   :caption: Features
   :maxdepth: 3

   features/operation
   features/comparison
   features/allocation
   features/parsing
   features/formatting
   features/aggregation

.. toctree::
   :hidden:
   :caption: Advanced Features
   :maxdepth: 3

   features/currencies
   features/currency-conversion
   features/bitcoin

.. |clearfloat|  raw:: html

    <div style="clear:left"></div>
