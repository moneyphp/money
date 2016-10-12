# Change Log


## Unreleased

### Changed

- **[BC break]** Replaced StringToUnitsParser with DecimalMoneyParser

### Added

- DecimalMoneyFormatter: returns locale-independent raw decimal string

## 3.0.0-beta.3 - 2016-10-04

### Changed

- **[BC break]** Convert method now moved to its own class: Converter
- **[BC break]** Exchange had one method getCurrencyPair which is now renamed to quote
- Minor documentation issues

### Added

- FixedExchange: returns fixed exchange rates based on a list (array)

### Fixed

- Integer detection when the number overflows the integer type and contains zeros
- Rounding numbers containg trailing zeros
- Converting Money to currency with different number of subunits

## 3.0.0-beta.2 - 2016-08-03

### Changed

- **[BC break]** Dropped PHP 5.4 support
- **[BC break]** Intl and Bitcoin formatters and parsers now require Currencies
- ISOCurrencies now uses moneyphp/iso-currencies as currency data source

### Added

- PHP Spec tests
- absolute method to Money and Calculator
- subunitFor method to Currencies
- Currencies now extends IteratorAggregate
- Library exceptions now implement a common interface
- Formatter and Parser implementation are now rounding half up

### Fixed

- Documentation to be inline with upcoming version 3
- Rounding issues in calculators with negative numbers
- Formatting and parser issues for amounts and numbers with a trailing zero
- Improved many exception messages
- Registration of own Calculator implementations

## 3.0.0-beta - 2016-03-01

### Added

- Bitcoin parser and formatter
- Also checking tests folder for StyleCI

### Fixed

- Currencies are now included in the repo
- Currency list generation moved to dev dependency: reduces repo size
- BC Math calculator adding and subtracting failed when bcscale was set
- Parsing zero for StringToUnitsParser

## 3.0.0-alpha - 2016-02-04

### Added

- Currency repositories (ISO currencies included)
- Money exchange (including [Swap](https://github.com/florianv/swap) implementation)
- Money formatting (including intl formatter)
- Money parsing (including intl parser)
- Big integer support utilizing different, transparent calculation logic upon availability (bcmath, gmp, plain php)
- Money and Currency implements JsonSerializable
- Rounding up and down
- Allocation to N targets

### Changed

- **[BC break]** Money::getAmount() returns a string instead of an int value
- **[BC break]** Moved stringToUnits to StringToUnitsParser parser
- Library requires at least PHP 5.4
- Library uses PSR-4

### Fixed

- Integer overflow

### Removed

- **[BC break]** UnkownCurrency exception
- **[BC break]** Currency list is now provided by [umpirsky/currency-list](https://github.com/umpirsky/currency-list/)
- **[BC break]** RoundingMode class
- **[BC break]** Announced deprecations are removed (Currency::getName, CurrencyPair::getRatio, Money::getUnits)


## Pre 3.0

- 2015-03-23 Minimum php version is now 5.4
- 2015-03-23 JsonSerializable
- (... missing changelog because who remembers to document stuff anyway?)
- 2014-03-22 Removed \Money\InvalidArgumentException in favour of plain old InvalidArgumentException
- 2014-03-22 Introduce RoundingMode object, used to specify desired rounding
- 2014-03-22 Introduced RoundingMode backwards compatible API changes to Money::multiply and Money::divide
- 2014-03-22 Allow RoundingMode to be specified when converting currencies
- 2014-03-22 CurrencyPair has an equals() method
- 2013-10-13 Base currency and counter currency in CurrencyPair named correctly.
- 2013-01-08 Removed the Doctrine2\MoneyType helper, to be replaced by something better in the future. It's available
             at https://gist.github.com/4485025 in case you need it.
- 2013-01-08 Use vendor/autoload.php instead of lib/bootstrap.php (or use PSR-0 autolaoding)
- 2012-12-10 Renamed Money::getUnits() to Money::getAmount()
