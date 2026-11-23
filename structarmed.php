<?php

declare(strict_types=1);

use Boundwize\StructArmed\Architecture;
use Boundwize\StructArmed\Preset\Preset;

return Architecture::define()
    ->withPresets(Preset::PSR4(), Preset::CODEQUALITY())
    ->layer('Currency', 'src/Currency.php')
    ->layer('Number', 'src/Number.php')
    ->layer('Exception', [
        'src/Exception.php',
        'src/Exception',
    ])
    ->layer('Money', [
        'src/Money.php',
        'src/MoneyFactory.php',
        'src/Calculator.php',
        'src/Calculator',
    ])
    ->layer('Currencies', [
        'src/Currencies.php',
        'src/Currencies',
    ])
    ->layer('CurrencyPair', 'src/CurrencyPair.php')
    ->layer('Exchange', [
        'src/Exchange.php',
        'src/Exchange',
    ])
    ->layer('Converter', 'src/Converter.php')
    ->layer('Formatter', [
        'src/MoneyFormatter.php',
        'src/Formatter',
    ])
    ->layer('Parser', [
        'src/MoneyParser.php',
        'src/Parser',
    ])
    ->layer('Teller', [
        'src/Teller.php',
        'src/TellerFactory.php',
    ])
    ->layer('PHPUnit', 'src/PHPUnit')
    ->ruleset([
        'Currency'     => ['Money'],
        'Number'       => [],
        'Exception'    => ['Currency'],
        'Money'        => ['+Exception', 'Number'],
        'Currencies'   => ['+Exception'],
        'CurrencyPair' => ['Currency'],
        'Exchange'     => ['+Currencies', '+CurrencyPair', '+Money'],
        'Converter'    => ['+Exchange'],
        'Formatter'    => ['+Currencies', '+Money'],
        'Parser'       => ['+Currencies', '+Money'],
        'Teller'       => ['+Formatter', '+Parser'],
        'PHPUnit'      => ['+Formatter'],
    ]);
