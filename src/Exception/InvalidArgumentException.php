<?php

declare(strict_types=1);

namespace Money\Exception;

use InvalidArgumentException as CoreInvalidArgumentException;
use Money\Exception;

/**
 * @internal
 */
class InvalidArgumentException extends CoreInvalidArgumentException implements Exception
{
    /** @phpstan-pure */
    public static function divisionByZero(): DivisionByZeroException
    {
        return new DivisionByZeroException('Cannot compute division with a zero divisor');
    }

    /** @phpstan-pure */
    public static function moduloByZero(): ModuloByZeroException
    {
        return new ModuloByZeroException('Cannot compute modulo with a zero divisor');
    }

    /** @phpstan-pure */
    public static function currencyMismatch(): CurrencyMismatchException
    {
        return new CurrencyMismatchException('Currencies must be identical');
    }
}
