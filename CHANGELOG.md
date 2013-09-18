Money Changelog
===============


2.0.0.dev
---------

- 2013-08-18 Introduce RoundingMode object, used to specify desired rounding
- 2013-08-18 Introduced RoundingMode backwards compatible API changes to Money::multiply and Money::divide
- 2013-08-18 Allow RoundingMode to be specified when converting currencies
- 2013-01-08 Removed the Doctrine2\MoneyType helper, to be replaced by something better in the future. It's available
             at https://gist.github.com/4485025 in case you need it.
- 2013-01-08 Use vendor/autoload.php instead of lib/bootstrap.php (or use PSR-0 autolaoding)
- 2012-12-10 Renamed Money::getUnits() to Money::getAmount()