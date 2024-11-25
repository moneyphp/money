<?php

declare(strict_types=1);

namespace Tests\Money;

use Closure;

use function setlocale;

trait Locale
{
    public static function runLocaleAware(int $category, string $locale, Closure $callback): void
    {
        // @phpstan-ignore-next-line
        $currentLocale = setlocale($category, 0);
        try {
            // @phpstan-ignore-next-line
            setlocale($category, $locale);
            $callback();
        } finally {
            // @phpstan-ignore-next-line
            setlocale($category, $currentLocale);
        }
    }
}
