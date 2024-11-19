<?php
declare(strict_types=1);

namespace App\Service;

use App\Constant\CalculateFeesConstant;
use App\Entity\Operation;
use App\Service\Contract\FeesCalculationInterface;

class WithdrawCalculatorService implements FeesCalculationInterface
{
    private array $commissionStrategies;

    public function __construct()
    {
        $this->commissionStrategies = [
            CalculateFeesConstant::PRIVATE_CLIENT =>
                fn(Operation $operation) => $this->calculatePrivateClientCommission($operation),
            CalculateFeesConstant::BUSINESS_CLIENT =>
                fn(Operation $operation) => $this->calculateBusinessClientCommission($operation),
        ];
    }

    public function calculate(Operation $operation): float
    {
        $userType = $operation->getUserType();

        if (!isset($this->commissionStrategies[$userType])) {
            throw new \InvalidArgumentException("Unsupported user type: " . $operation->getUserType());
        }

        return ($this->commissionStrategies[$userType])($operation);
    }

    public function supports(Operation $operation): bool
    {
        return $operation->getType() === CalculateFeesConstant::OPERATION_TYPE_WITHDRAW;
    }

    private function calculatePrivateClientCommission(Operation $operation): float
    {
        $userId = $operation->getUserId();
        $weekKey = $this->generateWeekKey($operation->getDate(), $userId);

        $userBalance = DataRegistry::getData();
        $userTransactionsData = $userBalance[$weekKey] ?? ['totalAmount' => 0, 'freeOperations' => 0];
        $userBalance[$weekKey] = &$userTransactionsData;
        $exchangedAmount = $operation->getAmount() / $operation->getRate();
        $commission = $this->applyPrivateClientCommissionRules($userTransactionsData, $exchangedAmount);
        DataRegistry::setData($userBalance);
        return ceil($commission * $operation->getRate() * 100) / 100;
    }

    private function calculateBusinessClientCommission(Operation $operation): float
    {
        return ceil($operation->getAmount() * CalculateFeesConstant::WITHDRAW_BUSINESS_FEE * 100) / 100;
    }

    private function generateWeekKey(\DateTime $date, int $userId): string
    {
        return $userId . '_' . $date->format('o-W');
    }

    private function applyPrivateClientCommissionRules(array &$userTransactionsData, float $exchangedAmount): float
    {
        if ($this->isWithinFreeLimit($userTransactionsData, $exchangedAmount)) {
            $userTransactionsData['totalAmount'] += $exchangedAmount;
            $userTransactionsData['freeOperations']++;
            return 0.0;
        }

        if ($this->isPartialFreeLimit($userTransactionsData)) {
            return $this->calculatePartialCommission($userTransactionsData, $exchangedAmount);
        }

        return $exchangedAmount * CalculateFeesConstant::WITHDRAW_PRIVATE_FEE;
    }

    private function isWithinFreeLimit(array $userTransactionsData, float $exchangedAmount): bool
    {
        return $userTransactionsData['freeOperations'] < CalculateFeesConstant::PRIVATE_FREE_OPERATIONS
            && $userTransactionsData['totalAmount'] + $exchangedAmount <= CalculateFeesConstant::PRIVATE_WEEKLY_LIMIT;
    }

    private function isPartialFreeLimit(array $userTransactionsData): bool
    {
        return $userTransactionsData['totalAmount'] < CalculateFeesConstant::PRIVATE_WEEKLY_LIMIT;
    }

    private function calculatePartialCommission(array &$userTransactionsData, float $exchangedAmount): float
    {
        $freeAmountRemaining = CalculateFeesConstant::PRIVATE_WEEKLY_LIMIT - $userTransactionsData['totalAmount'];
        $amountToCharge = max(0, $exchangedAmount - $freeAmountRemaining);
        $commission = $amountToCharge * CalculateFeesConstant::WITHDRAW_PRIVATE_FEE;

        $userTransactionsData['totalAmount'] = CalculateFeesConstant::PRIVATE_WEEKLY_LIMIT;
        $userTransactionsData['freeOperations']++;

        return $commission;
    }
}
