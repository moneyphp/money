<?php

require __DIR__.'/../vendor/autoload.php';

use Money\Currencies;

$buffer = <<<PHP
<?php

namespace Money;

/**
 * This is a generated file. Do not edit it manually!
 *
PHPDOC
 */
trait MoneyFactory {}

PHP;

$methodBuffer = '';

$currencies = new Currencies\AggregateCurrencies([
    new Currencies\ISOCurrencies(),
    new Currencies\BitcoinCurrencies(),
]);

/** @var \Money\Currency[] $currencies */
foreach ($currencies as $currency) {
    $methodBuffer .= sprintf(" * @method static Money %s(string \$amount)\n", $currency->getCode());
}

$buffer = str_replace('PHPDOC', rtrim($methodBuffer), $buffer);

file_put_contents(__DIR__.'/../src/MoneyFactory.php', $buffer);
