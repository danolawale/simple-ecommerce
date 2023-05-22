<?php
declare(strict_types=1);

namespace App\Command\Shipper;

use App\Integration\Shipper\EasyPost\EasyPostOrderShipmentServiceInterface;
use App\Integration\Shipper\ShipOrderFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:ship:easypost:order')]
class EasyPostOrderShipmentCommand extends Command
{
    public function __construct(
        private readonly EasyPostOrderShipmentServiceInterface $easyPostOrderShipmentService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('To run on the terminal: php bin/console app:ship:easypost:order -o {order-ref}')
            ->setDescription(
                'This command helps ship orders on easypost.'
            )
            ->addOption('order', 'o', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getOption('order')) {
            $output->writeln("Enter a valid and unique reference for the order to ship");
            return Command::FAILURE;
        }

        $orderRef = $input->getOption('order');

        $shipmentInfo = $this->easyPostOrderShipmentService->createShipment($orderRef);
        $shipmentDetails = $this->easyPostOrderShipmentService->buyShipment($shipmentInfo);

        $orderId = $shipmentDetails['orderId'];
        $shipmentId = $shipmentDetails['shipmentId'];
        $label = $shipmentDetails['label'];

        $output->writeln("\nOrder $orderId has been shipped successfully. Shipment Id = $shipmentId\n");

        if ($label) {
            $output->writeln("Label is $label\n");
        }

        return Command::SUCCESS;
    }
}
#744356