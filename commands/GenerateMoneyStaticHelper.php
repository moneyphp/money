<?php

namespace Commands\Money;

use Money\Currencies\AggregateCurrencies;

class GenerateMoneyStaticHelper extends AbstractGenerateMoneyStatic
{
    protected $name = 'generate:money-static:helper';
    protected $description = 'Generates a helper class with static methods.';
    protected $help = 'Generates a Money helper class with all overloaded static methods.';

    protected $filepath = '..';
    protected $filename = '_ide_helper.money.php';

    protected function generateFile(AggregateCurrencies $currencies)
    {
        $this->pushBuffer('<?php');
        $this->pushBuffer('');
        $this->pushBuffer('namespace Money;');
        $this->pushBuffer('');
        $this->pushBuffer('class Money');
        $this->pushBuffer('{');
        foreach ($currencies as $currency) {
            $code = $currency->getCode();
            $this->pushBuffer(
                $this->getIndent(1) . 'public static function ' . $code . '($amount)' . PHP_EOL .
                $this->getIndent(1) . '{' . PHP_EOL .
                $this->getIndent(2) . 'return new self($amount, new Currency(\'' . $code . '\'));' . PHP_EOL .
                $this->getIndent(1) . '}' . PHP_EOL
            );
        }
        $this->buffer = trim($this->buffer) . PHP_EOL;
        $this->pushBuffer('}');
    }

    protected function pushBuffer($line)
    {
        $this->buffer .= $line . PHP_EOL;
    }

    protected function getIndent($amount = 1)
    {
        return str_repeat(str_repeat(' ', $this->indent), $amount);
    }
}
