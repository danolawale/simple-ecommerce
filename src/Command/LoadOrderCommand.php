<?php
declare(strict_types=1);

namespace App\Command;

use App\Integration\Retailer\LoadCustomerOrderServiceInterface;
use App\Integration\Retailer\LoadOrderFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:load:customer:orders')]
class LoadOrderCommand extends Command
{
    public function __construct(private readonly LoadOrderFactory $loadOrderFactory)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('To run on the terminal: php bin/console app:load:customer:orders')
            ->setDescription(
                'This command helps to load and store orders from retailers such as ebay, etsy, etc'
            )
            ->addOption('platform', 'p', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getOption('platform')) {
            $output->writeln("Enter a platform to load orders from e.g ebay");
            return Command::FAILURE;
        }

        $order = ($this->loadOrderFactory)($input->getOption('platform'))->load();
        $output->writeln("\nOrder $order loaded successfully\n");

        return Command::SUCCESS;
    }

}