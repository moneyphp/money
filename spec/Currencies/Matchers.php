<?php

namespace spec\Money\Currencies;

use Money\Currency;

trait Matchers
{
    public function getMatchers(): array
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
