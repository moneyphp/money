# Change Log

## 3.0.0-alpha

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

- Library requires at least PHP 5.4
- Library uses PSR-4

### Fixed

- Integer overflow

### Removed

- UnkownCurrency exception
- Currency list is now provided by [umpirsky/currency-list](https://github.com/umpirsky/currency-list/)
- RoundingMode class


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
