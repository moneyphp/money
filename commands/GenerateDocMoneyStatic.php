<?php
namespace Commands\Money;


use Money\Currencies\AggregateCurrencies;
use Money\Currencies\BitcoinCurrencies;
use Money\Currencies\ISOCurrencies;
use Money\Money;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateDocMoneyStatic extends Command
{
    protected $buffer = '';

    protected $indent = 4;

    protected function configure()
    {
        $this
            ->setName('generate:doc:money-static')
            ->setDescription('Generates a helper class with static methods.')
            ->setHelp('Generates a Money helper class with all overloaded static methods.')

            ->addOption('path', null, InputOption::VALUE_REQUIRED, 'The filepath where to store the helper file.', realpath(__DIR__.'/..'))
            ->addOption('currencies', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Additional currencies classes to load.')
            ->addOption('indent', null, InputOption::VALUE_REQUIRED, 'The amount of spaces to use for indent.', 4);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indent = $input->getOption('indent');
        if(is_numeric($indent) && is_int($indent * 1) && $indent >= 0) {
            $this->indent = $indent;
        }

        $currenciesClasses = [
            new ISOCurrencies(),
            new BitcoinCurrencies(),
        ];

        foreach($input->getOption('currencies') as $currenciesClass) {
            if(!class_exists($currenciesClass)) {
                $output->writeln('<fg=yellow>'.$currenciesClass.' is no class</>');
                continue;
            }
            $currenciesClasses[] = new $currenciesClass();
        }

        try {
            $currencies = new AggregateCurrencies($currenciesClasses);
        } catch(\InvalidArgumentException $ex) {
            $output->writeln('<fg=red>'.$ex->getMessage().'</>');
            return;
        }

        $path = $input->getOption('path');
        if(!is_dir($path)) {
            $output->writeln('<fg=red>'.$path.' does not exist</>');
            return;
        }

        $filepath = realpath($path).'/_ide_helper.money.php';

        $output->writeln('<fg=blue>Generate '.Money::class.' static method helper class</>');
        $output->writeln('<fg=magenta>Target-File:</>');
        $output->writeln($filepath);
        $output->writeln('<fg=magenta>Currencies-Classes:</>');
        $output->writeln(array_map('get_class', $currenciesClasses));

        $output->writeln('<fg=magenta>Found-Currencies:</>');
        $output->writeln(iterator_count($currencies));

        $this->pushBuffer('<?php');
        $this->pushBuffer('namespace Money;');
        $this->pushBuffer('');
        $this->pushBuffer('class Money');
        $this->pushBuffer('{');
        foreach($currencies as $currency) {
            $code = $currency->getCode();
            $this->pushBuffer(
                $this->getIndent(1).'public static function '.$code.'($amount) {'.PHP_EOL.
                $this->getIndent(2).'return new self($amount, new Currency(\''.$code.'\'));'.PHP_EOL.
                $this->getIndent(1).'}'.PHP_EOL
            );
        }
        $this->buffer = trim($this->buffer).PHP_EOL;
        $this->pushBuffer('}');

        if(file_put_contents($filepath, $this->buffer)) {
            $output->writeln('<fg=green>helper class successfully written</>');
            return;
        }
        $output->writeln('<fg=red>an error occurred and the file was not written</>');
    }

    protected function pushBuffer($line)
    {
        $this->buffer .= $line.PHP_EOL;
    }

    protected function getIndent($amount = 1)
    {
        return str_repeat(str_repeat(' ', $this->indent), $amount);
    }
}
