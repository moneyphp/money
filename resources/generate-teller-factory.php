<?php

require __DIR__.'/../vendor/autoload.php';

use Money\Currencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;
use Money\Teller;

(static function (): void {
    $buffer = <<<'PHP'
<?php

declare(strict_types=1);

namespace Money;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Parser\DecimalMoneyParser;

use function array_shift;

/**
 * This is a generated file. Do not edit it manually!
 *
PHPDOC
 * @psalm-immutable
 */
trait TellerFactory
{
    /**
     * Convenience factory method for a Teller object.
     *
     * <code>
     * $teller = Teller::USD();
     * </code>
     *
     * @param non-empty-string          $method
     * @param array{0?: Money::ROUND_*} $arguments
     */
    public static function __callStatic(string $method, array $arguments): Teller
    {
        $currency     = new Currency($method);
        $currencies   = new ISOCurrencies();
        $parser       = new DecimalMoneyParser($currencies);
        $formatter    = new DecimalMoneyFormatter($currencies);
        $roundingMode = empty($arguments)
            ? Money::ROUND_HALF_UP
            : (int) array_shift($arguments);

        return new Teller(
            $currency,
            $parser,
            $formatter,
            $roundingMode
        );
    }
}

PHP;

    $methodBuffer = '';

    $iterator = new Currencies\AggregateCurrencies([
        new Currencies\ISOCurrencies(),
        new Currencies\BitcoinCurrencies(),
        new Currencies\CryptoCurrencies(),
    ]);

    $currencies = array_unique([...$iterator]);
    usort($currencies, static fn (Currency $a, Currency $b): int => strcmp($a->getCode(), $b->getCode()));

    /** @var Currency[] $currencies */
    foreach ($currencies as $currency) {
        $code = $currency->getCode();
        if (is_numeric($code[0])) {
            preg_match('/^([0-9]*)(.*?)$/', $code, $extracted);

            $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
            $code = strtoupper(preg_replace('/\s+/', '', $formatter->format($extracted[1])) . $extracted[2]);
        }

        $methodBuffer .= sprintf(" * @method static Teller %s(int \$roundingMode = Money::ROUND_HALF_UP)\n", $code);
    }

    $buffer = str_replace('PHPDOC', rtrim($methodBuffer), $buffer);

    file_put_contents(__DIR__.'/../src/TellerFactory.php', $buffer);
})();
