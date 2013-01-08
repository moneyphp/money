Money
=====

[![Build Status](https://secure.travis-ci.org/mathiasverraes/money.png)](http://travis-ci.org/mathiasverraes/money)

PHP 5.3+ library to make working with money safer, easier, and fun!

In short: You probably shouldn't represent monetary values by a float. Wherever
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

See [`MoneyBundle`][2] for [Symfony integration][3].

[1]: http://getcomposer.org/
[2]: https://github.com/pink-tie/MoneyBundle/
[3]: http://symfony.com/
