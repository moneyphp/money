<?php

require __DIR__.'/../vendor/autoload.php';

use Money\Currencies;
use Money\Currency;

(static function (): void {
    $buffer = <<<'PHP'
<?php

declare(strict_types=1);

namespace Money;

use InvalidArgumentException;

/**
 * This is a generated file. Do not edit it manually!
 *
PHPDOC
 *
 * @psalm-immutable
 */
trait MoneyFactory
{
    /**
     * Convenience factory method for a Money object.
     *
     * <code>
     * $fiveDollar = Money::USD(500);
     * </code>
     *
     * @param string $method
     * @param array  $arguments
     * @psalm-param empty $arguments
     *
     * @throws InvalidArgumentException If amount is not integer(ish).
     *
     * @psalm-pure
     */
    public static function __callStatic(string $method, array $arguments): Money
    {
        return new Money($arguments[0], new Currency($method));
    }
}

PHP;

    $methodBuffer = '';

    $currencies = iterator_to_array(new Currencies\AggregateCurrencies([
        new Currencies\ISOCurrencies(),
        new Currencies\BitcoinCurrencies(),
    ]));

    usort($currencies, static fn (Currency $a, Currency $b): int => strcmp($a->getCode(), $b->getCode()));

    /** @var Currency[] $currencies */
    foreach ($currencies as $currency) {
        $methodBuffer .= sprintf(" * @method static Money %s(numeric-string|int \$amount)\n", $currency->getCode());
    }

    $buffer = str_replace('PHPDOC', rtrim($methodBuffer), $buffer);

    file_put_contents(__DIR__.'/../src/MoneyFactory.php', $buffer);
})();
