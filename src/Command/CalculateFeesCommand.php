<?php
declare(strict_types=1);
namespace App\Command;

use App\Constant\CalculateFeesConstant;
use App\Entity\Operation;
use App\Service\Client\ExchangeRateApiService;
use App\Service\FeesCalculatorService;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
#[AsCommand(
    name:  'calculate:fees',
    description: 'Calculates commission fees from CSV deposit and withdrawal data.',
)]
class CalculateFeesCommand extends Command
{
    private FeesCalculatorService $feesCalculatorService;
    private ExchangeRateApiService $exchangeRateApiService;
    public function __construct(
        FeesCalculatorService $feesCalculatorService,
        ExchangeRateApiService $exchangeRateApiService
    )
    {
        $this->feesCalculatorService = $feesCalculatorService;
        $this->exchangeRateApiService = $exchangeRateApiService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('csvFile', InputArgument::REQUIRED, 'CSV file with transactions data');
    }

    /**
     * @throws \DateMalformedStringException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rates = $this->exchangeRateApiService->getExchangeRates();
        $filePath = $input->getArgument('csvFile');
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new RuntimeException("The file at path '$filePath' does not exist or is not readable.");
        }

        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, null, CalculateFeesConstant::CSV_DELIMITER)) !== false) {
                $commission = $this->feesCalculatorService->calculate($this->createOperationFromCsvData($data,$rates));
                $output->writeln(number_format($commission, 2, '.', ''));
            }
            fclose($handle);
        }

        return Command::SUCCESS;
    }

    /**
     * @throws \DateMalformedStringException
     */
    private function createOperationFromCsvData(array $data, $rates): Operation
    {
        [$date, $userId, $userType, $operationType, $amount, $currency] = $data;
        return new Operation(
            new \DateTime($date),
            (int) $userId,
            $userType,
            $operationType,
            (float) $amount,
            $currency,
            $rates[$currency]
        );
    }
}