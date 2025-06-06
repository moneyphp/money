<?php

declare(strict_types=1);

namespace Money;

use InvalidArgumentException;

use function abs;
use function explode;
use function is_int;
use function ltrim;
use function min;
use function rtrim;
use function sprintf;
use function str_pad;
use function strlen;
use function substr;

/**
 * Represents a numeric value.
 *
 * @internal this is an internal utility of the library, and may vary at any time. It is mostly used to internally validate
 *           that a number is represented at digits, but by improving type system integration, we may be able to completely
 *           get rid of it.
 *
 * @phpstan-immutable
 */
final class Number
{
    /** @phpstan-var numeric-string */
    private string $integerPart;

    /** @phpstan-var numeric-string|'' */
    private string $fractionalPart;
    private const NUMBERS = [0 => 1, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1];

    /**
     * @phpstan-pure
     */
    public function __construct(string $integerPart, string $fractionalPart = '')
    {
        if ($integerPart === '' && $fractionalPart === '') {
            throw new InvalidArgumentException('Empty number is invalid');
        }

        $this->integerPart    = self::parseIntegerPart($integerPart);
        $this->fractionalPart = self::parseFractionalPart($fractionalPart);
    }

    /** @phpstan-pure */
    public static function fromString(string $number): self
    {
        $portions = explode('.', $number, 2);

        return new self(
            $portions[0],
            rtrim($portions[1] ?? '', '0')
        );
    }

    /** @phpstan-pure */
    public static function fromFloat(float $number): self
    {
        return self::fromString(sprintf('%.14F', $number));
    }

    /** @phpstan-pure */
    public static function fromNumber(int|string $number): self
    {
        if (is_int($number)) {
            return new self((string) $number);
        }

        return self::fromString($number);
    }

    /**
     * @phpstan-pure
     */
    public function isDecimal(): bool
    {
        return $this->fractionalPart !== '';
    }

    /**
     * @phpstan-pure
     */
    public function isInteger(): bool
    {
        return $this->fractionalPart === '';
    }

    /**
     * @phpstan-pure
     */
    public function isHalf(): bool
    {
        return $this->fractionalPart === '5';
    }

    /**
     * @phpstan-pure
     */
    public function isCurrentEven(): bool
    {
        $lastIntegerPartNumber = (int) $this->integerPart[strlen($this->integerPart) - 1];

        return $lastIntegerPartNumber % 2 === 0;
    }

    /**
     * @phpstan-pure
     */
    public function isCloserToNext(): bool
    {
        if ($this->fractionalPart === '') {
            return false;
        }

        return $this->fractionalPart[0] >= 5;
    }

    /**
     * @phpstan-return numeric-string
     *
     * @phpstan-pure
     */
    public function __toString(): string
    {
        if ($this->fractionalPart === '') {
            return $this->integerPart;
        }

        return $this->integerPart . '.' . $this->fractionalPart;
    }

    /**
     * @phpstan-pure
     */
    public function isNegative(): bool
    {
        return $this->integerPart[0] === '-';
    }

    /**
     * @phpstan-return numeric-string
     *
     * @phpstan-pure
     */
    public function getIntegerPart(): string
    {
        return $this->integerPart;
    }

    /**
     * @phpstan-return numeric-string|''
     *
     * @phpstan-pure
     */
    public function getFractionalPart(): string
    {
        return $this->fractionalPart;
    }

    /**
     * @phpstan-return numeric-string
     *
     * @phpstan-pure
     */
    public function getIntegerRoundingMultiplier(): string
    {
        if ($this->integerPart[0] === '-') {
            return '-1';
        }

        return '1';
    }

    public function base10(int $number): self
    {
        if ($this->integerPart === '0' && ! $this->fractionalPart) {
            return $this;
        }

        $sign        = '';
        $integerPart = $this->integerPart;

        if ($integerPart[0] === '-') {
            $sign        = '-';
            $integerPart = substr($integerPart, 1);
        }

        if ($number >= 0) {
            $integerPart       = ltrim($integerPart, '0');
            $lengthIntegerPart = strlen($integerPart);
            $integers          = $lengthIntegerPart - min($number, $lengthIntegerPart);
            $zeroPad           = $number - min($number, $lengthIntegerPart);

            return new self(
                $sign . substr($integerPart, 0, $integers),
                rtrim(str_pad('', $zeroPad, '0') . substr($integerPart, $integers) . $this->fractionalPart, '0')
            );
        }

        $number               = abs($number);
        $lengthFractionalPart = strlen($this->fractionalPart);
        $fractions            = $lengthFractionalPart - min($number, $lengthFractionalPart);
        $zeroPad              = $number - min($number, $lengthFractionalPart);

        return new self(
            $sign . ltrim($integerPart . substr($this->fractionalPart, 0, $lengthFractionalPart - $fractions) . str_pad('', $zeroPad, '0'), '0'),
            substr($this->fractionalPart, $lengthFractionalPart - $fractions)
        );
    }

    /**
     * @phpstan-return numeric-string
     *
     * @phpstan-pure
     */
    private static function parseIntegerPart(string $number): string
    {
        if ($number === '' || $number === '0') {
            return '0';
        }

        if ($number === '-' || $number === '-0') {
            return '-0';
        }

        // Happy path performance optimization: number can be used as-is if it is within
        // the platform's integer capabilities.
        if ($number === (string) (int) $number) {
            return $number;
        }

        $nonZero = false;

        for ($position = 0, $characters = strlen($number); $position < $characters; ++$position) {
            $digit = $number[$position];

            if (! isset(self::NUMBERS[$digit]) && ! ($position === 0 && $digit === '-')) {
                throw new InvalidArgumentException(sprintf('Invalid integer part %1$s. Invalid digit %2$s found', $number, $digit));
            }

            if ($digit === '-') {
                continue;
            }

            if ($nonZero === false && $digit === '0') {
                throw new InvalidArgumentException('Leading zeros are not allowed');
            }

            $nonZero = true;
        }

        return $number;
    }

    /**
     * @phpstan-return numeric-string|''
     *
     * @phpstan-pure
     */
    private static function parseFractionalPart(string $number): string
    {
        if ($number === '') {
            return $number;
        }

        $intFraction = (int) $number;

        // Happy path performance optimization: number can be used as-is if it is within
        // the platform's integer capabilities, and it starts with zeroes only.
        if ($intFraction > 0 && ltrim($number, '0') === (string) $intFraction) {
            return $number;
        }

        for ($position = 0, $characters = strlen($number); $position < $characters; ++$position) {
            $digit = $number[$position];

            if (! isset(self::NUMBERS[$digit])) {
                throw new InvalidArgumentException(sprintf('Invalid fractional part %1$s. Invalid digit %2$s found', $number, $digit));
            }
        }

        return $number;
    }

    /**
     * @phpstan-pure
     */
    public static function roundMoneyValue(string $moneyValue, int $targetDigits, int $havingDigits): string
    {
        $valueLength = strlen($moneyValue);
        $shouldRound = $targetDigits < $havingDigits && $valueLength - $havingDigits + $targetDigits > 0;

        if ($shouldRound && $moneyValue[$valueLength - $havingDigits + $targetDigits] >= 5) {
            $position = $valueLength - $havingDigits + $targetDigits;
            $addend   = 1;

            while ($position > 0) {
                // @phpstan-ignore possiblyImpure.methodCall (no idea what it thinks this)
                $newValue = (string) ((int) $moneyValue[$position - 1] + $addend);

                if ($newValue >= 10) {
                    $moneyValue[$position - 1] = $newValue[1];

                    $addend = $newValue[0];
                    --$position;
                    if ($position === 0) {
                        $moneyValue = $addend . $moneyValue;
                    }
                } else {
                    if ($moneyValue[$position - 1] === '-') {
                        $moneyValue[$position - 1] = $newValue[0];
                        $moneyValue                = '-' . $moneyValue;
                    } else {
                        $moneyValue[$position - 1] = $newValue[0];
                    }

                    break;
                }
            }
        }

        return $moneyValue;
    }
}
