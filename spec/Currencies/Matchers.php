<?php

declare(strict_types=1);

namespace spec\Money\Currencies;

use Money\Currency;

use function assert;
use function is_iterable;

trait Matchers
{
    /** @psalm-return non-empty-array<non-empty-string, callable(mixed, mixed): bool> */
    public function getMatchers(): array
    {
        return [
            'haveCurrency' => static function (mixed $subject, mixed $value): bool {
                assert(is_iterable($subject));

                foreach ($subject as $currency) {
                    assert($currency instanceof Currency);
                    if ($currency->getCode() === $value) {
                        return true;
                    }
                }

                return false;
            },
        ];
    }
}
