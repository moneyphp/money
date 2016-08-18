Money
=====

[![Build Status](https://api.travis-ci.org/moneyphp/money.png?branch=master)](http://travis-ci.org/moneyphp/money)

PHP 5.5+ library to make working with money safer, easier, and fun!

> "If I had a dime for every time I've seen someone use FLOAT to store currency, I'd have $999.997634" -- [Bill Karwin](https://twitter.com/billkarwin/status/347561901460447232)

In short: You shouldn't represent monetary values by a float. Wherever
you need to represent money, use this Money value object. Since version
3.0 this library uses [strings internally](https://github.com/moneyphp/money/pull/136)
in order to support unlimited integers.

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

Install the library using [composer][1]. It is listed on [Packagist][5].

``` bash
composer require mathiasverraes/money
```

Features
--------

- JSON Serialization
- Big integer support utilizing different, transparent calculation logic upon availability (bcmath, gmp, plain php)
- Money formatting (including intl formatter)
- Currency repositories (ISO currencies included)
- Money exchange (including Swap implementation)

Integration
-----------

See [`MoneyBundle`][2] or [`TbbcMoneyBundle`][4] for [Symfony integration][3].

Testing
-------
```bash
$ composer test
```

Contributing
------------

We would love to see you helping us to make this library better and better. Please keep in mind we do not use suffixes
and prefixes in class names, so not `CurrenciesInterface` but `Currencies`. Other than that, Style CI will help you
using the same code style as we are using. Please provide tests when creating a PR and clear descriptions of bugs when
filing issues.

License
-------

Money is licensed under the MIT License - see the `LICENSE` file for details

Acknowledgements
----------------

This library is heavily inspired by Martin Fowler's Money pattern. A special remark goes to Mathias Verraes, without his
contributions, in code and via his blog, this library would not be where it stands now.

[1]: http://getcomposer.org/
[2]: https://github.com/pink-tie/MoneyBundle/
[3]: http://symfony.com/
[4]: https://github.com/TheBigBrainsCompany/TbbcMoneyBundle
[5]: https://packagist.org/packages/mathiasverraes/money
