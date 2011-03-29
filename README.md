Verraes\Money
=============

This is a PHP implementation of the Money pattern, as described in [Fowler2002].

The problem
-----------

From [Fowler2002]:

> A large proportion of the computers in this world manipulate money, so it's always puzzled me 
> that money isn't actually a first class data type in any mainstream programming language. The 
> lack of a type causes problems, the most obvious surrounding currencies. If all your calculations 
> are done in a single currency, this isn't a huge problem, but once you involve multiple currencies 
> you want to avoid adding your dollars to your yen without taking the currency differences into 
> account. The more subtle problem is with rounding. Monetary calculations are often rounded to the 
> smallest currency unit. When you do this it's easy to lose pennies (or your local equivalent) 
> because of rounding errors.

The goal
--------

Implement a reusable Money class in PHP, using all the best practices and taking care of all the
subtle intricacies of handling money.

Usage
=====

<?php
use Verraes\Money\Money,
    Verraes\Money\Usd,
    Verraes\Money\Euro;

// One EURO, expressed in cents
$eur1 = new Money(100, new Euro);
// Shortcut
$eur2 = Money::euro(200);

Money::euro(300)->equals(
   $eur1->add($eur2)
);

Inspiration
===========

* https://github.com/RubyMoney/money
* http://css.dzone.com/books/practical-php-patterns/basic/practical-php-patterns-value

* http://www.codeproject.com/KB/recipes/MoneyTypeForCLR.aspx
* http://www.michaelbrumm.com/money.html
* http://stackoverflow.com/questions/1679292/proof-that-fowlers-money-allocation-algorithm-is-correct
* http://timeandmoney.sourceforge.net/
* https://github.com/lucamarrocco/timeandmoney/blob/master/lib/money.rb

Bibliography
============

[Fowler2002]
Fowler, M., D. Rice, M. Foemmel, E. Hieatt, R. Mee, and R. Stafford, Patterns of Enterprise Application Architecture, Addison-Wesley, 2002.
http://martinfowler.com/books.html#eaa

http://en.wikipedia.org/wiki/ISO_4217

Todo
====

* https://github.com/RubyMoney/eu_central_bank