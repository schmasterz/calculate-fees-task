<?php
declare(strict_types=1);
namespace App\Service;

use App\Constant\CalculateFeesConstant;
use App\Entity\Operation;
use App\Service\Contract\FeesCalculationInterface;

class DepositCalculatorService implements FeesCalculationInterface
{
    public function calculate(Operation $operation): float
    {
        return ceil($operation->getAmount() * CalculateFeesConstant::DEPOSIT_FEE * 100) / 100;
    }

    public function supports(Operation $operation): bool
    {
        return $operation->getType() === CalculateFeesConstant::OPERATION_TYPE_DEPOSIT;
    }
}