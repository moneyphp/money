<?php
namespace Money;

final class IntlMoneyFormatter implements MoneyFormatter {

    /**
     * @var string
     */
    private $locale;
    /**
     * @var int
     */
    private $fractionDigits;

    /**
     * IntlMoneyFormatter constructor.
     * @param int $fractionDigits
     * @param string $locale
     */
    public function __construct($locale, $fractionDigits)
    {
        if (extension_loaded('intl') === false) {
            throw new \RuntimeException(
                'Cannot initialize IntlMoneyFormatter because intl extension is missing'
            );
        }

        $this->locale = $locale;
        $this->fractionDigits = $fractionDigits;
    }

    /**
     * {@inheritdoc}
     */
    public function format(Money $money)
    {
        $valueBase = (string) $money->getAmount();
        $valueLength = strlen($valueBase);
        if ($valueLength > $this->fractionDigits) {
            $subunits = substr($valueBase, 0, $valueLength - $this->fractionDigits) . '.';
            $subunits .= substr($valueBase, $valueLength - $this->fractionDigits);
        } else {
            $subunits = "0." . str_pad('', $this->fractionDigits - $valueLength, '0') . $valueBase;
        }


        $formatter = new \NumberFormatter($this->locale, \NumberFormatter::CURRENCY);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $this->fractionDigits);
        return $formatter->formatCurrency($subunits, $money->getCurrency()->getCode());
    }
}
