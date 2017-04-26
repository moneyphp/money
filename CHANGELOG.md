# Change Log

## 3.0.5 - 2017-04-26

### Added

- numericCodeFor method to ISOCurrencies


## 3.0.4 - 2017-04-21

### Fixed

- ISOCurrencies will no longer have a blank currency
- Double symbol when formatting negative Bitcoin amounts 

### Added

- Negative method

### Changed

- Updated ISO Currencies
- Removed old Belarusian ruble from ISOCurrencies (BYR)


## 3.0.3 - 2017-03-22

### Fixed

- Parsing empty strings and number starting or ending with a decimal point for DecimalMoneyParser
- Parsing zero for DecimalMoneyParser
- Multiplying and dividing with a locale that use commas as separator

## 3.0.2 - 2017-03-11

### Fixed

- BCMath / GMP: comparing values smaller than one
- GMP: multiplying with zero
- ISOCurrencies: minor refactoring, remove duplication of code


## 3.0.1 - 2017-02-14

### Added

- Reversed Currencies Exchange to try resolving reverse of a currency pair
- Documentation on allowed integer(ish) values when constructing Money

### Fixed

- Passing integer validation when chunk started with a dash
- Passing integer validation when the fractional part started with a dash
- Formatting problem for Bitcoin currency with small amounts in PHP < 7.0
- Money constructed from a string with fractional zeroes equals to a Money constructed without the fractional part (eg. `'5.00'` and `'5'`)


## 3.0.0 - 2016-10-26

### Added

- DecimalMoneyFormatter: returns locale-independent raw decimal string

### Changed

- **[BC break]** Replaced StringToUnitsParser with DecimalMoneyParser
- **[BC break]** Moved `Money\Exception\Exception` to `Money\Exception`
- **[BC break]** UnkownCurrencyException is now DomainException instead of RuntimeException
- **[Doctrine break]** In `Currency` the private variable `name` was renamed to `code`, which could break your Doctrine mapping if you are using embeddables or any other Reflection related implementation.


## 3.0.0-beta.3 - 2016-10-04

### Added

- FixedExchange: returns fixed exchange rates based on a list (array)

### Changed

- **[BC break]** Convert method now moved to its own class: Converter
- **[BC break]** Exchange had one method getCurrencyPair which is now renamed to quote
- Minor documentation issues

### Fixed

- Integer detection when the number overflows the integer type and contains zeros
- Rounding numbers containg trailing zeros
- Converting Money to currency with different number of subunits


## 3.0.0-beta.2 - 2016-08-03

### Added

- PHP Spec tests
- absolute method to Money and Calculator
- subunitFor method to Currencies
- Currencies now extends IteratorAggregate
- Library exceptions now implement a common interface
- Formatter and Parser implementation are now rounding half up

### Changed

- **[BC break]** Dropped PHP 5.4 support
- **[BC break]** Intl and Bitcoin formatters and parsers now require Currencies
- ISOCurrencies now uses moneyphp/iso-currencies as currency data source

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
