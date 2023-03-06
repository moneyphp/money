<?php

namespace Tests\Money\Exchange;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Money\Exchange\CurrencyPair;
use Money\Exchange\CurrencyPair as CurrencyPairContract;
use Money\Exchange\FixedExchange;
use Money\Exchange\IndirectExchange;
use Money\Exchange\ReversedCurrenciesExchange;
use PHPUnit\Framework\TestCase;

/** @covers \Money\Exchange\CurrencyPair */
class CustomExchangeAndCurrencyPairTest extends TestCase
{
    public function test_it_can_use_a_different_currency_pair_object()
    {
        $base            = new Currency('EUR');
        $counter         = new Currency('USD');
        $wrappedExchange = $this->createMock(Exchange::class);

        $wrappedExchange->method('quote')
            ->with(self::equalTo($base), self::equalTo($counter))
            ->willReturn(new CustomCurrencyPair($base, $counter, '1.25', 'local'));

        self::assertEquals(
            new CustomCurrencyPair($base, $counter, '1.25', 'local'),
            (new ReversedCurrenciesExchange($wrappedExchange))
                ->quote($base, $counter)
        );
    }
}

final class CustomCurrencyPair implements CurrencyPairContract
{
    /**
     * Currency to convert from.
     */
    private Currency $baseCurrency;

    /**
     * Currency to convert to.
     */
    private Currency $counterCurrency;

    /** @psalm-var numeric-string */
    private string $conversionRatio;

    private string $providerName;

    /**
     * @psalm-param numeric-string $conversionRatio
     */
    public function __construct(Currency $baseCurrency, Currency $counterCurrency, string $conversionRatio, string $provider)
    {
        $this->counterCurrency = $counterCurrency;
        $this->baseCurrency    = $baseCurrency;
        $this->conversionRatio = $conversionRatio;
        $this->providerName = $provider;
    }

    /**
     * Creates a new Currency Pair based on "EUR/USD 1.2500" form representation.
     *
     * @param string $iso String representation of the form "EUR/USD 1.2500"
     *
     * @throws InvalidArgumentException Format of $iso is invalid.
     */
    public static function createFromIso(string $iso): \Money\Exchange\CurrencyPair
    {
        $currency = '([A-Z]{2,3})';
        $ratio    = '([0-9]*\.?[0-9]+)'; // @see http://www.regular-expressions.info/floatingpoint.html
        $pattern  = '#' . $currency . '/' . $currency . ' ' . $ratio . '#';

        $matches = [];

        if (! preg_match($pattern, $iso, $matches)) {
            throw new InvalidArgumentException(sprintf('Cannot create currency pair from ISO string "%s", format of string is invalid', $iso));
        }

        assert($matches[1] !== '');
        assert($matches[2] !== '');
        assert(is_numeric($matches[3]));

        return new self(new Currency($matches[1]), new Currency($matches[2]), $matches[3]);
    }

    /**
     * Returns the counter currency.
     */
    public function getCounterCurrency(): Currency
    {
        return $this->counterCurrency;
    }

    public function getProviderName(): string
    {
        return $this->providerName;
    }

    /**
     * Returns the base currency.
     */
    public function getBaseCurrency(): Currency
    {
        return $this->baseCurrency;
    }

    /**
     * Returns the conversion ratio.
     *
     * @psalm-return numeric-string
     */
    public function getConversionRatio(): string
    {
        return $this->conversionRatio;
    }

    /**
     * Checks if an other CurrencyPair has the same parameters as this.
     */
    public function equals(CurrencyPairContract $other): bool
    {
        return $this->baseCurrency->equals($other->baseCurrency)
            && $this->counterCurrency->equals($other->counterCurrency)
            && $this->conversionRatio === $other->conversionRatio;
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-return array{baseCurrency: Currency, counterCurrency: Currency, ratio: numeric-string}
     */
    public function jsonSerialize(): array
    {
        return [
            'baseCurrency' => $this->baseCurrency,
            'counterCurrency' => $this->counterCurrency,
            'ratio' => $this->conversionRatio,
        ];
    }
}
