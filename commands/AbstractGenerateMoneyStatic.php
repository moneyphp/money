<?php

namespace Commands\Money;


use Money\Currencies\AggregateCurrencies;
use Money\Currencies\BitcoinCurrencies;
use Money\Currencies\ISOCurrencies;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractGenerateMoneyStatic extends Command
{
    protected $name = null;
    protected $description = null;
    protected $help = null;

    protected $filepath = null;
    protected $filename = null;

    protected $buffer = '';

    protected $indent = 4;

    protected function configure()
    {
        $this
            ->setName($this->name)
            ->setDescription($this->description)
            ->setHelp($this->help)
            ->addOption('path', null, InputOption::VALUE_REQUIRED, 'The filepath where to store the file.', realpath(__DIR__ . '/' . $this->filepath))
            ->addOption('currencies', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Additional currencies classes to load.')
            ->addOption('indent', null, InputOption::VALUE_REQUIRED, 'The amount of spaces to use for indent.', 4);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indent = $input->getOption('indent');
        if (is_numeric($indent) && is_int($indent * 1) && $indent >= 0) {
            $this->indent = $indent;
        }

        $currenciesClasses = [
            new ISOCurrencies(),
            new BitcoinCurrencies(),
        ];

        foreach ($input->getOption('currencies') as $currenciesClass) {
            if (!class_exists($currenciesClass)) {
                $output->writeln('<fg=yellow>' . $currenciesClass . ' is no class</>');
                continue;
            }
            $currenciesClasses[] = new $currenciesClass();
        }

        try {
            $currencies = new AggregateCurrencies($currenciesClasses);
        } catch (\InvalidArgumentException $ex) {
            $output->writeln('<fg=red>' . $ex->getMessage() . '</>');
            return;
        }

        $path = $input->getOption('path');
        if (!is_dir($path)) {
            $output->writeln('<fg=red>' . $path . ' does not exist</>');
            return;
        }

        $filepath = realpath($path) . '/'.$this->filename;

        $output->writeln('<fg=magenta>Target-File:</>');
        $output->writeln($filepath);
        $output->writeln('<fg=magenta>Currencies-Classes:</>');
        $output->writeln(array_map('get_class', $currenciesClasses));

        $output->writeln('<fg=magenta>Found-Currencies:</>');
        $output->writeln(iterator_count($currencies));

        $this->generateFile($currencies);

        if (file_put_contents($filepath, $this->buffer)) {
            $output->writeln('<fg=green>file successfully written</>');
            return;
        }
        $output->writeln('<fg=red>an error occurred and the file was not written</>');
    }

    abstract protected function generateFile(AggregateCurrencies $currencies);

    protected function pushBuffer($line)
    {
        $this->buffer .= $line . PHP_EOL;
    }

    protected function getIndent($amount = 1)
    {
        return str_repeat(str_repeat(' ', $this->indent), $amount);
    }
}
