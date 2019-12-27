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
trait MoneyRangeFactory
{
    /**
     * Convenience factory method for a MoneyRange object.
     *
     * <code>
     * \$fiveToTenDollars = MoneyRange::USD(500, 1000);
     * </code>
     *
     * @param string \$method
     * @param array  \$arguments
     *
     * @return MoneyRange
     *
     * @throws \InvalidArgumentException If either amount is not integer(ish)
     */
    public static function __callStatic(\$method, \$arguments)
    {
        return new MoneyRange(
            new Money(\$arguments[0], new Currency(\$method)),
            new Money(\$arguments[1], new Currency(\$method))
        );
    }
}

PHP;

$methodBuffer = '';

$currencies = new Currencies\AggregateCurrencies([
    new Currencies\ISOCurrencies(),
    new Currencies\BitcoinCurrencies(),
]);

$currencies = iterator_to_array($currencies);

usort($currencies, function (\Money\Currency $a, \Money\Currency $b) {
    return strcmp($a->getCode(), $b->getCode());
});

/** @var \Money\Currency[] $currencies */
foreach ($currencies as $currency) {
    $methodBuffer .= sprintf(" * @method static MoneyRange %s(string \$amount)\n", $currency->getCode());
}

$buffer = str_replace('PHPDOC', rtrim($methodBuffer), $buffer);

file_put_contents(__DIR__.'/../src/MoneyRangeFactory.php', $buffer);
