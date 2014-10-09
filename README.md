Money
=====

[![Build Status](https://api.travis-ci.org/mathiasverraes/money.png?branch=master)](http://travis-ci.org/mathiasverraes/money)

PHP 5.3+ library to make working with money safer, easier, and fun!

> "If I had a dime for every time I've seen someone use FLOAT to store currency, I'd have $999.997634" -- [Bill Karwin](https://twitter.com/billkarwin/status/347561901460447232)

In short: You shouldn't represent monetary values by a float. Wherever
you need to represent money, use this Money value object.

```php
<?php

use Money\Money;

$fiveEur = Money::EUR(500);
$tenEur = $fiveEur->add($fiveEur);

list($part1, $part2, $part3) = $tenEur->allocate(array(1, 1, 1));
assert($part1->equals(Money::EUR(334)));
assert($part2->equals(Money::EUR(333)));
assert($part3->equals(Money::EUR(333)));
```

The documentation is available at http://money.readthedocs.org


Installation
------------

Install the library using [composer][1]. Add the following to your `composer.json`:

```json
{
    "require": {
        "mathiasverraes/money": "dev-master"
    },
    "minimum-stability": "dev"    
}
```

Now run the `install` command.

```sh
$ composer.phar install
```

Integration
-----------

See [`MoneyBundle`][2] or [`TbbcMoneyBundle`][4] for [Symfony integration][3] or [`DoctrineMoneyModule`][5] for [`Zend Framework 2`][6].

[1]: http://getcomposer.org/
[2]: https://github.com/pink-tie/MoneyBundle/
[3]: http://symfony.com/
[4]: https://github.com/TheBigBrainsCompany/TbbcMoneyBundle
[5]: https://github.com/zfbrasil/doctrine-money-module
[6]: http://framework.zend.com/
