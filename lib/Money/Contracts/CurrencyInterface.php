<?php
/**
 * This file is part of the Money library
 *
 * Copyright (c) 2011-2013 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money\Contracts;

interface CurrencyInterface
{
	/**
     * @return string
     */
    public function getName();

    /**
     * @param \Money\Contracts\CurrencyInterface $other
     * @return bool
     */
    public function equals(CurrencyInterface $other);
}