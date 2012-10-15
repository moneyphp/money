<?php
/**
 * This file is part of the Money library
 *
 * Copyright (c) 2011 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money;

use Money\Currency;

class Currencies
{
    const DOT = '.';
    const COMMA = ',';

    private static $ALL = array(
        
        Currency::EUR => array(
            'SYMBOL'  => '€',
            'DECIMAL_SEPARATOR'  => self::DOT,
            'THOUSAND_SEPARATOR' => self::COMMA,
        ),
        Currency::USD => array(
            'SYMBOL'  => '$',
            'DECIMAL_SEPARATOR'  => self::DOT,
            'THOUSAND_SEPARATOR' => self::COMMA,
        ),
        Currency::GBP => array(
            'SYMBOL'  => '$',
            'DECIMAL_SEPARATOR'  => self::DOT,
            'THOUSAND_SEPARATOR' => self::COMMA,
        ),
        Currency::JPY => array(
            'SYMBOL'  => '¥',
            'DECIMAL_SEPARATOR'  => '',
            'THOUSAND_SEPARATOR' => '',
        ),
        Currency::BRL => array(
            'SYMBOL'  => 'R$',
            'DECIMAL_SEPARATOR'  => self::COMMA,
            'THOUSAND_SEPARATOR' => self::DOT,
        ),
    );

    public static function all()
    {
        $list = array();
        
        foreach(array_keys(self::$ALL) as $key) {
            array_push($list, $key);
        }

        return $list;
    }

    public static function exist($name)
    {
        return in_array($name, array_keys(self::$ALL));
    }

    public static function getSymbol($name)
    {
        return self::$ALL[$name]['SYMBOL'];
    }

    public static function getDecimalSeparator($name)
    {
        return self::$ALL[$name]['DECIMAL_SEPARATOR'];
    }

    public static function getThousandSeparator($name)
    {
        return self::$ALL[$name]['THOUSAND_SEPARATOR'];
    }
}