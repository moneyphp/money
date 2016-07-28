<?php

namespace Money\SubUnit;

use Money\Currency;
use Money\Exception\UnknownISOCurrencyException;
use Money\SubUnitProvider;

final class ISOProvider implements SubUnitProvider
{
    /**
     * List of known currencies.
     *
     * @var array
     */
    private static $currencies;

    /**
     * {@inheritdoc}
     */
    public function provide(Currency $currency)
    {
        if (null === self::$currencies) {
            self::$currencies = $this->loadCurrencies();
        }

        if (!isset(self::$currencies[$currency->getCode()])) {
            throw new UnknownISOCurrencyException('Cannot find ISO currency '.$currency->getCode());
        }

        return self::$currencies[$currency->getCode()]['minorUnit'];
    }

    private function loadCurrencies()
    {
        $file = __DIR__.'/../../vendor/moneyphp/iso-currencies/resources/current.php';

        if (file_exists($file)) {
            return require $file;
        }

        throw new \RuntimeException('Failed to load currency ISO codes.');
    }
}
