<?php
namespace Money;

interface MoneyFormatter {

    /**
     * @param Money $money
     * @return string
     */
    public function format(Money $money);

}
