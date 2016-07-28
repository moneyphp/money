<?php

namespace Money\Exception;

/**
 * Thrown when trying to get ISO currency that does not exists.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class UnknownISOCurrencyException extends \RuntimeException implements Exception
{
}
