<?php

declare(strict_types=1);

namespace Tests\Money;

use Closure;

use function setlocale;

trait Locale
{
    public static function runLocaleAware(int $category, string $locale, Closure $callback): void
    {
        $currentLocale = setlocale($category, 0);
        try {
            setlocale($category, $locale);
            $callback();
        } finally {
            setlocale($category, $currentLocale);
        }
    }
}
