<?php

namespace spec\Money\Currencies;

trait HaveCurrencyTrait
{
    /**
     * @return array
     */
    public function getMatchers()
    {
        return [
            'haveCurrency' => function ($subject, $value) {
                foreach ($subject as $code) {
                    if ($code === $value) {
                        return true;
                    }
                }

                return false;
            },
        ];
    }
}
