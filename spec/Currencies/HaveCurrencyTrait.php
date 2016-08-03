<?php

namespace spec\Money\Currencies;

use Money\Currency;

trait HaveCurrencyTrait
{
    /**
     * @return array
     */
    public function getMatchers()
    {
        return [
            'haveCurrency' => function ($subject, $value) {
                /** @var Currency $currency */
                foreach ($subject as $currency) {
                    if ($currency->getCode() === $value) {
                        return true;
                    }
                }

                return false;
            },
        ];
    }
}
