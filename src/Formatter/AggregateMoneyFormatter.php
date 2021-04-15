<?php

declare(strict_types=1);

namespace Money\Formatter;

use InvalidArgumentException;
use Money\Exception\FormatterException;
use Money\Money;
use Money\MoneyFormatter;

use function sprintf;

/**
 * Formats a Money object using other Money formatters.
 */
final class AggregateMoneyFormatter implements MoneyFormatter
{
    /** @var MoneyFormatter[] */
    private array $formatters = [];

    /**
     * @param MoneyFormatter[] $formatters
     */
    public function __construct(array $formatters)
    {
        if (empty($formatters)) {
            throw new InvalidArgumentException(sprintf('Initialize an empty %s is not possible', self::class));
        }

        foreach ($formatters as $currencyCode => $formatter) {
            if ($formatter instanceof MoneyFormatter === false) {
                throw new InvalidArgumentException('All formatters must implement ' . MoneyFormatter::class);
            }

            $this->formatters[$currencyCode] = $formatter;
        }
    }

    public function format(Money $money): string
    {
        $currencyCode = $money->getCurrency()->getCode();

        if (isset($this->formatters[$currencyCode])) {
            return $this->formatters[$currencyCode]->format($money);
        }

        if (isset($this->formatters['*'])) {
            return $this->formatters['*']->format($money);
        }

        throw new FormatterException('No formatter found for currency ' . $currencyCode);
    }
}
