<?php
namespace Money;

interface MoneyParser
{

    /**
     * @param $formattedMoney
     * @param null $forceCurrency
     * @return Money
     * @throws ParserException
     */
    public function parse($formattedMoney, $forceCurrency = null);

}
