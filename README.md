# Money

[![Latest Version](https://img.shields.io/github/release/moneyphp/money.svg?style=flat-square)](https://github.com/moneyphp/money/releases)
[![Build Status](https://img.shields.io/travis/moneyphp/money.svg?style=flat-square)](https://travis-ci.org/moneyphp/money)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/moneyphp/money.svg?style=flat-square)](https://scrutinizer-ci.com/g/moneyphp/money)
[![Quality Score](https://img.shields.io/scrutinizer/g/moneyphp/money.svg?style=flat-square)](https://scrutinizer-ci.com/g/moneyphp/money)
[![Total Downloads](https://img.shields.io/packagist/dt/moneyphp/money.svg?style=flat-square)](https://packagist.org/packages/moneyphp/money)

[![Email](https://img.shields.io/badge/email-team@moneyphp.org-blue.svg?style=flat-square)](mailto:team@moneyphp.org)

![Money PHP](/resources/logo.png?raw=true)

PHP 5.5+ library to make working with money safer, easier, and fun!

> "If I had a dime for every time I've seen someone use FLOAT to store currency, I'd have $999.997634" -- [Bill Karwin](https://twitter.com/billkarwin/status/347561901460447232)

In short: You shouldn't represent monetary values by a float. Wherever
you need to represent money, use this Money value object. Since version
3.0 this library uses [strings internally](https://github.com/moneyphp/money/pull/136)
in order to support unlimited integers.

``` php
<?php

use Money\Money;

$fiveEur = Money::EUR(500);
$tenEur = $fiveEur->add($fiveEur);

list($part1, $part2, $part3) = $tenEur->allocate(array(1, 1, 1));
assert($part1->equals(Money::EUR(334)));
assert($part2->equals(Money::EUR(333)));
assert($part3->equals(Money::EUR(333)));
```

The documentation is available at http://moneyphp.org


## Install

Via Composer

``` bash
$ composer require moneyphp/money
```


## Features

- JSON Serialization
- Big integer support utilizing different, transparent calculation logic upon availability (bcmath, gmp, plain php)
- Money formatting (including intl formatter)
- Currency repositories (ISO currencies included)
- Money exchange (including [Swap](http://swap.voutzinos.org) implementation)


## Documentation

Please see the [official documentation](http://moneyphp.org).


## Testing

We try to follow BDD and TDD, as such we use both [phpspec](http://www.phpspec.net) and [phpunit](https://phpunit.de) to test this library.

``` bash
$ composer test
```


## Contributing

We would love to see you helping us to make this library better and better.
Please keep in mind we do not use suffixes and prefixes in class names,
so not `CurrenciesInterface`, but `Currencies`. Other than that, Style CI will help you
using the same code style as we are using. Please provide tests when creating a PR and clear descriptions of bugs when filing issues.


## Security

If you discover any security related issues, please contact us at [team@moneyphp.org](mailto:team@moneyphp.org).


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.


## Acknowledgements

This library is heavily inspired by [Martin Fowler's Money pattern](http://martinfowler.com/eaaCatalog/money.html).
A special remark goes to [Mathias Verraes](https://github.com/mathiasverraes), without his contributions,
in code and via his [blog](http://verraes.net/#blog), this library would not be where it stands now.
